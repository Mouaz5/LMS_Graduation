import 'package:equatable/equatable.dart';
import '../../../core/models/diagnostic_question_model.dart';
import '../../../core/models/subject_model.dart';

abstract class DiagnosticTestState extends Equatable {
  const DiagnosticTestState();
  @override
  List<Object?> get props => [];
}

class DiagnosticTestInitial extends DiagnosticTestState {
  const DiagnosticTestInitial();
}

class DiagnosticTestLoading extends DiagnosticTestState {
  const DiagnosticTestLoading();
}

class DiagnosticSubjectPicking extends DiagnosticTestState {
  final List<SubjectModel> subjects;
  final int? selectedSubjectId;

  const DiagnosticSubjectPicking({
    required this.subjects,
    this.selectedSubjectId,
  });

  DiagnosticSubjectPicking copyWith({int? selectedSubjectId}) {
    return DiagnosticSubjectPicking(
      subjects: subjects,
      selectedSubjectId: selectedSubjectId ?? this.selectedSubjectId,
    );
  }

  @override
  List<Object?> get props => [subjects, selectedSubjectId];
}

class DiagnosticInProgress extends DiagnosticTestState {
  final int attemptId;
  final List<DiagnosticQuestionModel> questions;
  final Map<int, int> answers;
  final bool isSubmitting;

  const DiagnosticInProgress({
    required this.attemptId,
    required this.questions,
    this.answers = const {},
    this.isSubmitting = false,
  });

  int get answeredCount => answers.length;
  int get totalCount => questions.length;
  bool get allAnswered => totalCount > 0 && answeredCount == totalCount;

  DiagnosticInProgress copyWith({
    Map<int, int>? answers,
    bool? isSubmitting,
  }) {
    return DiagnosticInProgress(
      attemptId: attemptId,
      questions: questions,
      answers: answers ?? this.answers,
      isSubmitting: isSubmitting ?? this.isSubmitting,
    );
  }

  @override
  List<Object?> get props => [attemptId, questions, answers, isSubmitting];
}

class DiagnosticSubmitted extends DiagnosticTestState {
  const DiagnosticSubmitted();
}

class DiagnosticTestError extends DiagnosticTestState {
  final String message;
  const DiagnosticTestError(this.message);
  @override
  List<Object?> get props => [message];
}
