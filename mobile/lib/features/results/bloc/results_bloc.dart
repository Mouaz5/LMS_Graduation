import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:open_filex/open_filex.dart';
import '../data/results_repository.dart';
import 'results_event.dart';
import 'results_state.dart';

class ResultsBloc extends Bloc<ResultsEvent, ResultsState> {
  final ResultsRepository _repository;
  final int _studentId;

  ResultsBloc({
    required ResultsRepository repository,
    required int studentId,
  })  : _repository = repository,
        _studentId = studentId,
        super(const ResultsInitial()) {
    on<ResultsLoadRequested>(_onLoadRequested);
    on<ResultsSemesterChanged>(_onSemesterChanged);
    on<ResultsPdfDownloadRequested>(_onPdfDownloadRequested);
  }

  Future<void> _onLoadRequested(
    ResultsLoadRequested event,
    Emitter<ResultsState> emit,
  ) async {
    emit(const ResultsLoading());
    try {
      final data = await _repository.fetchReportCard(studentId: _studentId);
      emit(ResultsLoaded(
        data: data,
        selectedSemesterId:
            data.semesters.isNotEmpty ? data.semesters.first.id : null,
      ));
    } on DioException catch (e) {
      emit(ResultsError(_parseError(e)));
    } catch (e) {
      emit(ResultsError(e.toString()));
    }
  }

  Future<void> _onSemesterChanged(
    ResultsSemesterChanged event,
    Emitter<ResultsState> emit,
  ) async {
    final current = state;
    if (current is! ResultsLoaded) return;
    emit(const ResultsLoading());
    try {
      final data = await _repository.fetchReportCard(
        studentId: _studentId,
        semesterId: event.semesterId,
      );
      emit(ResultsLoaded(data: data, selectedSemesterId: event.semesterId));
    } on DioException catch (e) {
      emit(ResultsError(_parseError(e)));
    } catch (e) {
      emit(ResultsError(e.toString()));
    }
  }

  Future<void> _onPdfDownloadRequested(
    ResultsPdfDownloadRequested event,
    Emitter<ResultsState> emit,
  ) async {
    final current = state;
    if (current is! ResultsLoaded || current.selectedSemesterId == null) return;

    emit(current.copyWith(isDownloadingPdf: true));
    try {
      final file = await _repository.downloadReportCardPdf(
        studentId: _studentId,
        semesterId: current.selectedSemesterId!,
      );
      emit(current.copyWith(isDownloadingPdf: false, downloadedPdf: file));
      await OpenFilex.open(file.path);
    } on DioException catch (e) {
      emit(ResultsError(_parseError(e)));
    } catch (e) {
      emit(ResultsError(e.toString()));
    }
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    if (e.response?.statusCode == 403) return 'Access denied.';
    return 'Failed to load results. Please try again.';
  }
}
