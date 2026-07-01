enum MasteryLevel { mastered, developing, needsWork, notStarted }

MasteryLevel _masteryLevelFromApi(String? level) => switch (level) {
      'Mastered' => MasteryLevel.mastered,
      'Developing' => MasteryLevel.developing,
      'NeedsWork' => MasteryLevel.needsWork,
      _ => MasteryLevel.notStarted,
    };

class LearningObjectiveModel {
  final int id;
  final String name;
  final String? description;
  final double? masteryPercent;
  final MasteryLevel level;
  final List<LearningObjectiveModel> children;

  const LearningObjectiveModel({
    required this.id,
    required this.name,
    this.description,
    this.masteryPercent,
    required this.level,
    this.children = const [],
  });

  factory LearningObjectiveModel.fromJson(Map<String, dynamic> json) {
    return LearningObjectiveModel(
      id: json['id'] as int,
      name: json['name'] as String,
      description: json['description'] as String?,
      masteryPercent: (json['mastery_percent'] as num?)?.toDouble(),
      level: _masteryLevelFromApi(json['level'] as String?),
      children: (json['children'] as List? ?? [])
          .map((e) =>
              LearningObjectiveModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }

  List<LearningObjectiveModel> flatten() {
    return [this, ...children.expand((c) => c.flatten())];
  }
}
