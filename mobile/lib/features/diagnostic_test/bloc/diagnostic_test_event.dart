import 'package:equatable/equatable.dart';

abstract class DiagnosticTestEvent extends Equatable {
  const DiagnosticTestEvent();
  @override
  List<Object?> get props => [];
}

class DiagnosticSubjectsLoadRequested extends DiagnosticTestEvent {
  const DiagnosticSubjectsLoadRequested();
}

class DiagnosticSubjectSelected extends DiagnosticTestEvent {
  final int subjectId;
  const DiagnosticSubjectSelected(this.subjectId);
  @override
  List<Object?> get props => [subjectId];
}

class DiagnosticTestStartRequested extends DiagnosticTestEvent {
  const DiagnosticTestStartRequested();
}

class DiagnosticAnswerSelected extends DiagnosticTestEvent {
  final int questionId;
  final int optionId;
  const DiagnosticAnswerSelected(this.questionId, this.optionId);
  @override
  List<Object?> get props => [questionId, optionId];
}

class DiagnosticSubmitRequested extends DiagnosticTestEvent {
  const DiagnosticSubmitRequested();
}
