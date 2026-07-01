class GradeModel {
  final int id;
  final int subjectId;
  final int examTypeId;
  final double score;
  final double maxScore;
  final String? subjectName;
  final String? examTypeName;
  final double? weightPercent;

  const GradeModel({
    required this.id,
    required this.subjectId,
    required this.examTypeId,
    required this.score,
    required this.maxScore,
    this.subjectName,
    this.examTypeName,
    this.weightPercent,
  });

  factory GradeModel.fromJson(Map<String, dynamic> json) {
    return GradeModel(
      id: json['id'] as int,
      subjectId: json['subject_id'] as int,
      examTypeId: json['exam_type_id'] as int,
      score: (json['score'] as num).toDouble(),
      maxScore: (json['max_score'] as num).toDouble(),
      subjectName: json['subject']?['name'] as String?,
      examTypeName: json['exam_type']?['name'] as String?,
      weightPercent: (json['exam_type']?['weight_percent'] as num?)?.toDouble(),
    );
  }
}

class GradeSummaryModel {
  final int id;
  final int subjectId;
  final String letterGrade;
  final double weightedAverage;
  final String? subjectName;

  const GradeSummaryModel({
    required this.id,
    required this.subjectId,
    required this.letterGrade,
    required this.weightedAverage,
    this.subjectName,
  });

  factory GradeSummaryModel.fromJson(Map<String, dynamic> json) {
    return GradeSummaryModel(
      id: json['id'] as int,
      subjectId: json['subject_id'] as int,
      letterGrade: json['letter_grade'] as String,
      weightedAverage: (json['weighted_average'] as num).toDouble(),
      subjectName: json['subject']?['name'] as String?,
    );
  }
}
