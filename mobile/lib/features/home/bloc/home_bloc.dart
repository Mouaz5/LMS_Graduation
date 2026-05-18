import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/models/classroom_model.dart';
import '../../../core/models/teacher_assignment_model.dart';
import '../../../core/services/api_service.dart';
import 'home_event.dart';
import 'home_state.dart';

class HomeBloc extends Bloc<HomeEvent, HomeState> {
  final ApiService _apiService;

  HomeBloc({required ApiService apiService})
      : _apiService = apiService,
        super(const HomeInitial()) {
    on<HomeLoadRequested>(_onLoadRequested);
  }

  Future<void> _onLoadRequested(
    HomeLoadRequested event,
    Emitter<HomeState> emit,
  ) async {
    emit(const HomeLoading());
    try {
      final classroomsResponse = await _apiService.dio.get('/classrooms');
      final assignmentsResponse = await _apiService.dio.get('/teacher-assignments');

      final classrooms = (classroomsResponse.data as List)
          .map((json) => ClassroomModel.fromJson(json as Map<String, dynamic>))
          .toList();

      final assignments = (assignmentsResponse.data as List)
          .map((json) => TeacherAssignmentModel.fromJson(json as Map<String, dynamic>))
          .toList();

      emit(HomeLoaded(classrooms: classrooms, assignments: assignments));
    } on DioException catch (e) {
      emit(HomeError(_parseError(e)));
    } catch (e) {
      emit(HomeError(e.toString()));
    }
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    return 'Failed to load data. Please try again.';
  }
}
