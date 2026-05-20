import 'package:equatable/equatable.dart';
import '../../../core/models/schedule_slot_model.dart';

abstract class ScheduleState extends Equatable {
  const ScheduleState();

  @override
  List<Object?> get props => [];
}

class ScheduleInitial extends ScheduleState {
  const ScheduleInitial();
}

class ScheduleLoading extends ScheduleState {
  const ScheduleLoading();
}

class ScheduleLoaded extends ScheduleState {
  final List<ScheduleSlotModel> slots;
  final String selectedDay;

  const ScheduleLoaded({required this.slots, required this.selectedDay});

  List<ScheduleSlotModel> get slotsForDay {
    final filtered = slots.where((s) => s.dayOfWeek == selectedDay).toList();
    filtered.sort((a, b) => a.periodNumber.compareTo(b.periodNumber));
    return filtered;
  }

  @override
  List<Object?> get props => [slots, selectedDay];
}

class ScheduleError extends ScheduleState {
  final String message;

  const ScheduleError(this.message);

  @override
  List<Object?> get props => [message];
}
