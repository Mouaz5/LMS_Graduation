class DiagnosticOptionModel {
  final int id;
  final String optionText;

  const DiagnosticOptionModel({required this.id, required this.optionText});

  factory DiagnosticOptionModel.fromJson(Map<String, dynamic> json) {
    return DiagnosticOptionModel(
      id: json['id'] as int,
      optionText: json['option_text'] as String,
    );
  }
}

class DiagnosticQuestionModel {
  final int id;
  final String questionText;
  final String type;
  final List<DiagnosticOptionModel> options;

  const DiagnosticQuestionModel({
    required this.id,
    required this.questionText,
    required this.type,
    required this.options,
  });

  factory DiagnosticQuestionModel.fromJson(Map<String, dynamic> json) {
    return DiagnosticQuestionModel(
      id: json['id'] as int,
      questionText: json['question_text'] as String,
      type: json['type'] as String,
      options: (json['options'] as List)
          .map((e) => DiagnosticOptionModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
