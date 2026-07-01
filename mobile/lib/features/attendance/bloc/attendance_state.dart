import 'package:equatable/equatable.dart';
import '../../../core/models/attendance_model.dart';

abstract class AttendanceState extends Equatable {
  const AttendanceState();
  @override
  List<Object?> get props => [];
}

class AttendanceInitial extends AttendanceState {
  const AttendanceInitial();
}

class AttendanceLoading extends AttendanceState {
  const AttendanceLoading();
}

class AttendanceLoaded extends AttendanceState {
  final AttendancePage page;
  final DateTime? dateFrom;
  final DateTime? dateTo;

  const AttendanceLoaded({
    required this.page,
    this.dateFrom,
    this.dateTo,
  });

  int get presentCount => _countWhere('present');
  int get absentCount => _countWhere('absent') + _countWhere('excused');
  int get lateCount => _countWhere('late');
  int get totalDays => page.records.length;

  double get attendanceRate {
    if (totalDays == 0) return 0;
    return (presentCount + lateCount) / totalDays * 100;
  }

  int _countWhere(String status) =>
      page.records.where((r) => r.status == status).length;

  @override
  List<Object?> get props => [page.records, dateFrom, dateTo];
}

class AttendanceError extends AttendanceState {
  final String message;
  const AttendanceError(this.message);
  @override
  List<Object?> get props => [message];
}
