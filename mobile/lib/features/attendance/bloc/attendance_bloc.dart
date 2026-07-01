import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../data/attendance_repository.dart';
import 'attendance_event.dart';
import 'attendance_state.dart';

class AttendanceBloc extends Bloc<AttendanceEvent, AttendanceState> {
  final AttendanceRepository _repository;
  final int _studentId;

  AttendanceBloc({
    required AttendanceRepository repository,
    required int studentId,
  })  : _repository = repository,
        _studentId = studentId,
        super(const AttendanceInitial()) {
    on<AttendanceLoadRequested>(_onLoadRequested);
    on<AttendanceDateRangeChanged>(_onDateRangeChanged);
  }

  Future<void> _onLoadRequested(
    AttendanceLoadRequested event,
    Emitter<AttendanceState> emit,
  ) async {
    final current = state;
    final dateFrom = current is AttendanceLoaded ? current.dateFrom : null;
    final dateTo = current is AttendanceLoaded ? current.dateTo : null;
    await _load(emit, dateFrom: dateFrom, dateTo: dateTo);
  }

  Future<void> _onDateRangeChanged(
    AttendanceDateRangeChanged event,
    Emitter<AttendanceState> emit,
  ) async {
    await _load(emit, dateFrom: event.dateFrom, dateTo: event.dateTo);
  }

  Future<void> _load(
    Emitter<AttendanceState> emit, {
    DateTime? dateFrom,
    DateTime? dateTo,
  }) async {
    emit(const AttendanceLoading());
    try {
      final page = await _repository.fetchAttendance(
        studentId: _studentId,
        dateFrom: dateFrom,
        dateTo: dateTo,
      );
      emit(AttendanceLoaded(page: page, dateFrom: dateFrom, dateTo: dateTo));
    } on DioException catch (e) {
      emit(AttendanceError(_parseError(e)));
    } catch (e) {
      emit(AttendanceError(e.toString()));
    }
  }

  String _parseError(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    if (e.response?.statusCode == 403) return 'Access denied.';
    return 'Failed to load attendance. Please try again.';
  }
}
