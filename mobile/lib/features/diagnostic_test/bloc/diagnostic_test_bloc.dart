import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/diagnostic_test_repository.dart';
import 'diagnostic_test_event.dart';
import 'diagnostic_test_state.dart';

class DiagnosticTestBloc extends Bloc<DiagnosticTestEvent, DiagnosticTestState> {
  final DiagnosticTestRepository _repository;

  DiagnosticTestBloc({required DiagnosticTestRepository repository})
      : _repository = repository,
        super(const DiagnosticTestInitial()) {
    on<DiagnosticSubjectsLoadRequested>(_onSubjectsLoadRequested);
    on<DiagnosticSubjectSelected>(_onSubjectSelected);
    on<DiagnosticTestStartRequested>(_onStartRequested);
    on<DiagnosticAnswerSelected>(_onAnswerSelected);
    on<DiagnosticSubmitRequested>(_onSubmitRequested);
  }

  Future<void> _onSubjectsLoadRequested(
    DiagnosticSubjectsLoadRequested event,
    Emitter<DiagnosticTestState> emit,
  ) async {
    emit(const DiagnosticTestLoading());
    try {
      final subjects = await _repository.fetchSubjects();
      emit(DiagnosticSubjectPicking(
        subjects: subjects,
        selectedSubjectId: subjects.isNotEmpty ? subjects.first.id : null,
      ));
    } on DioException catch (e) {
      emit(DiagnosticTestError(_parseError(e)));
    } catch (e) {
      emit(DiagnosticTestError(e.toString()));
    }
  }

  void _onSubjectSelected(
    DiagnosticSubjectSelected event,
    Emitter<DiagnosticTestState> emit,
  ) {
    final current = state;
    if (current is DiagnosticSubjectPicking) {
      emit(current.copyWith(selectedSubjectId: event.subjectId));
    }
  }

  Future<void> _onStartRequested(
    DiagnosticTestStartRequested event,
    Emitter<DiagnosticTestState> emit,
  ) async {
    final current = state;
    if (current is! DiagnosticSubjectPicking ||
        current.selectedSubjectId == null) {
      return;
    }
    emit(const DiagnosticTestLoading());
    try {
      final attemptId =
          await _repository.startAttempt(subjectId: current.selectedSubjectId!);
      final questions = await _repository.fetchQuestions(attemptId: attemptId);
      emit(DiagnosticInProgress(attemptId: attemptId, questions: questions));
    } on DioException catch (e) {
      emit(DiagnosticTestError(_parseError(e)));
    } catch (e) {
      emit(DiagnosticTestError(e.toString()));
    }
  }

  void _onAnswerSelected(
    DiagnosticAnswerSelected event,
    Emitter<DiagnosticTestState> emit,
  ) {
    final current = state;
    if (current is! DiagnosticInProgress) return;
    final answers = Map<int, int>.from(current.answers)
      ..[event.questionId] = event.optionId;
    emit(current.copyWith(answers: answers));
  }

  Future<void> _onSubmitRequested(
    DiagnosticSubmitRequested event,
    Emitter<DiagnosticTestState> emit,
  ) async {
    final current = state;
    if (current is! DiagnosticInProgress) return;
    emit(current.copyWith(isSubmitting: true));
    try {
      await _repository.submitAttempt(
        attemptId: current.attemptId,
        answers: current.answers,
      );
      emit(const DiagnosticSubmitted());
    } on DioException catch (e) {
      emit(DiagnosticTestError(_parseError(e)));
    } catch (e) {
      emit(DiagnosticTestError(e.toString()));
    }
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    if (e.response?.statusCode == 403) return 'Access denied.';
    return 'Something went wrong. Please try again.';
  }
}
