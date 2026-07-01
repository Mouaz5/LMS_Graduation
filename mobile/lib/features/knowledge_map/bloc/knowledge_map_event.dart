import 'package:equatable/equatable.dart';

abstract class KnowledgeMapEvent extends Equatable {
  const KnowledgeMapEvent();
  @override
  List<Object?> get props => [];
}

class KnowledgeMapSubjectsLoadRequested extends KnowledgeMapEvent {
  const KnowledgeMapSubjectsLoadRequested();
}

class KnowledgeMapSubjectSelected extends KnowledgeMapEvent {
  final int subjectId;
  const KnowledgeMapSubjectSelected(this.subjectId);
  @override
  List<Object?> get props => [subjectId];
}

class KnowledgeMapNodeToggled extends KnowledgeMapEvent {
  final int nodeId;
  const KnowledgeMapNodeToggled(this.nodeId);
  @override
  List<Object?> get props => [nodeId];
}
