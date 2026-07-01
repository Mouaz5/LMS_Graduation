import '../../../core/models/diagnostic_question_model.dart';
import '../../../core/models/subject_model.dart';
import '../../../core/services/api_service.dart';

class DiagnosticTestRepository {
  final ApiService _apiService;

  const DiagnosticTestRepository({required ApiService apiService})
      : _apiService = apiService;

  Future<List<SubjectModel>> fetchSubjects() async {
    final response = await _apiService.dio.get('/subjects');
    return (response.data as List)
        .map((e) => SubjectModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<int> startAttempt({required int subjectId}) async {
    final response = await _apiService.dio.post(
      '/v1/diagnostic-attempts',
      data: {'subject_id': subjectId},
    );
    return (response.data as Map<String, dynamic>)['attempt_id'] as int;
  }

  Future<List<DiagnosticQuestionModel>> fetchQuestions(
      {required int attemptId}) async {
    final response =
        await _apiService.dio.get('/v1/diagnostic-attempts/$attemptId/questions');
    final questions =
        (response.data as Map<String, dynamic>)['questions'] as List;
    return questions
        .map((e) =>
            DiagnosticQuestionModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<void> submitAttempt({
    required int attemptId,
    required Map<int, int> answers,
  }) async {
    await _apiService.dio.post(
      '/v1/diagnostic-attempts/$attemptId/submit',
      data: {
        'answers': answers.entries
            .map((e) => {
                  'question_id': e.key,
                  'selected_option_id': e.value,
                })
            .toList(),
      },
    );
  }
}
