import 'package:equatable/equatable.dart';
import '../../../core/models/classroom_model.dart';
import '../../../core/models/teacher_assignment_model.dart';

abstract class HomeState extends Equatable {
  const HomeState();

  @override
  List<Object?> get props => [];
}

class HomeInitial extends HomeState {
  const HomeInitial();
}

class HomeLoading extends HomeState {
  const HomeLoading();
}

class HomeLoaded extends HomeState {
  final List<ClassroomModel> classrooms;
  final List<TeacherAssignmentModel> assignments;

  const HomeLoaded({
    required this.classrooms,
    required this.assignments,
  });

  @override
  List<Object?> get props => [classrooms, assignments];
}

class HomeError extends HomeState {
  final String message;

  const HomeError(this.message);

  @override
  List<Object?> get props => [message];
}
