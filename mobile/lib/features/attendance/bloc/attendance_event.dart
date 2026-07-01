import 'package:equatable/equatable.dart';

abstract class AttendanceEvent extends Equatable {
  const AttendanceEvent();
  @override
  List<Object?> get props => [];
}

class AttendanceLoadRequested extends AttendanceEvent {
  const AttendanceLoadRequested();
}

class AttendanceDateRangeChanged extends AttendanceEvent {
  final DateTime? dateFrom;
  final DateTime? dateTo;

  const AttendanceDateRangeChanged({this.dateFrom, this.dateTo});

  @override
  List<Object?> get props => [dateFrom, dateTo];
}
