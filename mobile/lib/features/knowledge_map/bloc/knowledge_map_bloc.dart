import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/knowledge_map_repository.dart';
import 'knowledge_map_event.dart';
import 'knowledge_map_state.dart';

class KnowledgeMapBloc extends Bloc<KnowledgeMapEvent, KnowledgeMapState> {
  final KnowledgeMapRepository _repository;
  final int _studentId;

  KnowledgeMapBloc({
    required KnowledgeMapRepository repository,
    required int studentId,
  })  : _repository = repository,
        _studentId = studentId,
        super(const KnowledgeMapInitial()) {
    on<KnowledgeMapSubjectsLoadRequested>(_onSubjectsLoadRequested);
    on<KnowledgeMapSubjectSelected>(_onSubjectSelected);
    on<KnowledgeMapNodeToggled>(_onNodeToggled);
  }

  Future<void> _onSubjectsLoadRequested(
    KnowledgeMapSubjectsLoadRequested event,
    Emitter<KnowledgeMapState> emit,
  ) async {
    emit(const KnowledgeMapLoading());
    try {
      final subjects = await _repository.fetchSubjects();
      if (subjects.isEmpty) {
        emit(KnowledgeMapLoaded(subjects: subjects));
        return;
      }
      final firstId = subjects.first.id;
      final tree = await _repository.fetchKnowledgeMap(
        studentId: _studentId,
        subjectId: firstId,
      );
      emit(KnowledgeMapLoaded(
          subjects: subjects, selectedSubjectId: firstId, tree: tree));
    } on DioException catch (e) {
      emit(KnowledgeMapError(_parseError(e)));
    } catch (e) {
      emit(KnowledgeMapError(e.toString()));
    }
  }

  Future<void> _onSubjectSelected(
    KnowledgeMapSubjectSelected event,
    Emitter<KnowledgeMapState> emit,
  ) async {
    final current = state;
    if (current is! KnowledgeMapLoaded) return;
    emit(const KnowledgeMapLoading());
    try {
      final tree = await _repository.fetchKnowledgeMap(
        studentId: _studentId,
        subjectId: event.subjectId,
      );
      emit(current.copyWith(
        selectedSubjectId: event.subjectId,
        tree: tree,
        expandedNodeIds: const {},
      ));
    } on DioException catch (e) {
      emit(KnowledgeMapError(_parseError(e)));
    } catch (e) {
      emit(KnowledgeMapError(e.toString()));
    }
  }

  void _onNodeToggled(
    KnowledgeMapNodeToggled event,
    Emitter<KnowledgeMapState> emit,
  ) {
    final current = state;
    if (current is! KnowledgeMapLoaded) return;
    final expanded = Set<int>.from(current.expandedNodeIds);
    if (!expanded.remove(event.nodeId)) {
      expanded.add(event.nodeId);
    }
    emit(current.copyWith(expandedNodeIds: expanded));
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    if (e.response?.statusCode == 403) return 'Access denied.';
    return 'Failed to load knowledge map. Please try again.';
  }
}
