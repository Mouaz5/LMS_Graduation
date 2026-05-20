import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/models/schedule_slot_model.dart';
import '../../../core/services/api_service.dart';
import 'schedule_event.dart';
import 'schedule_state.dart';

class ScheduleBloc extends Bloc<ScheduleEvent, ScheduleState> {
  final ApiService _apiService;

  ScheduleBloc({required ApiService apiService})
      : _apiService = apiService,
        super(const ScheduleInitial()) {
    on<ScheduleLoadRequested>(_onLoadRequested);
    on<ScheduleDayChanged>(_onDayChanged);
  }

  Future<void> _onLoadRequested(
    ScheduleLoadRequested event,
    Emitter<ScheduleState> emit,
  ) async {
    emit(const ScheduleLoading());
    try {
      final response = await _apiService.dio.get('/v1/schedule-slots/my');

      final slots = (response.data as List)
          .map((json) => ScheduleSlotModel.fromJson(json as Map<String, dynamic>))
          .toList();

      emit(ScheduleLoaded(slots: slots, selectedDay: 'sunday'));
    } on DioException catch (e) {
      emit(ScheduleError(_parseError(e)));
    } catch (e) {
      emit(ScheduleError(e.toString()));
    }
  }

  void _onDayChanged(
    ScheduleDayChanged event,
    Emitter<ScheduleState> emit,
  ) {
    final current = state;
    if (current is ScheduleLoaded) {
      emit(ScheduleLoaded(slots: current.slots, selectedDay: event.day));
    }
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    if (e.response?.statusCode == 403) return 'Access denied.';
    return 'Failed to load schedule. Please try again.';
  }
}
