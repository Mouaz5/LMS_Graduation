import 'package:equatable/equatable.dart';
import '../../../core/models/learning_objective_model.dart';
import '../../../core/models/subject_model.dart';

abstract class KnowledgeMapState extends Equatable {
  const KnowledgeMapState();
  @override
  List<Object?> get props => [];
}

class KnowledgeMapInitial extends KnowledgeMapState {
  const KnowledgeMapInitial();
}

class KnowledgeMapLoading extends KnowledgeMapState {
  const KnowledgeMapLoading();
}

class KnowledgeMapLoaded extends KnowledgeMapState {
  final List<SubjectModel> subjects;
  final int? selectedSubjectId;
  final List<LearningObjectiveModel> tree;
  final Set<int> expandedNodeIds;

  const KnowledgeMapLoaded({
    required this.subjects,
    this.selectedSubjectId,
    this.tree = const [],
    this.expandedNodeIds = const {},
  });

  List<LearningObjectiveModel> get flatAssessed => tree
      .expand((n) => n.flatten())
      .where((n) => n.masteryPercent != null)
      .toList();

  int get masteredCount =>
      flatAssessed.where((n) => n.level == MasteryLevel.mastered).length;
  int get developingCount =>
      flatAssessed.where((n) => n.level == MasteryLevel.developing).length;
  int get needsWorkCount =>
      flatAssessed.where((n) => n.level == MasteryLevel.needsWork).length;

  double? get averageMastery {
    if (flatAssessed.isEmpty) return null;
    final sum =
        flatAssessed.fold<double>(0, (acc, n) => acc + n.masteryPercent!);
    return sum / flatAssessed.length;
  }

  KnowledgeMapLoaded copyWith({
    List<SubjectModel>? subjects,
    int? selectedSubjectId,
    List<LearningObjectiveModel>? tree,
    Set<int>? expandedNodeIds,
  }) {
    return KnowledgeMapLoaded(
      subjects: subjects ?? this.subjects,
      selectedSubjectId: selectedSubjectId ?? this.selectedSubjectId,
      tree: tree ?? this.tree,
      expandedNodeIds: expandedNodeIds ?? this.expandedNodeIds,
    );
  }

  @override
  List<Object?> get props =>
      [subjects, selectedSubjectId, tree, expandedNodeIds];
}

class KnowledgeMapError extends KnowledgeMapState {
  final String message;
  const KnowledgeMapError(this.message);
  @override
  List<Object?> get props => [message];
}
