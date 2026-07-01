import '../../../core/models/learning_objective_model.dart';
import '../../../core/models/subject_model.dart';
import '../../../core/services/api_service.dart';

class KnowledgeMapRepository {
  final ApiService _apiService;

  const KnowledgeMapRepository({required ApiService apiService})
      : _apiService = apiService;

  Future<List<SubjectModel>> fetchSubjects() async {
    final response = await _apiService.dio.get('/subjects');
    return (response.data as List)
        .map((e) => SubjectModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<LearningObjectiveModel>> fetchKnowledgeMap({
    required int studentId,
    required int subjectId,
  }) async {
    final response = await _apiService.dio.get(
      '/v1/knowledge-map',
      queryParameters: {'student_id': studentId, 'subject_id': subjectId},
    );
    final tree = (response.data as Map<String, dynamic>)['tree'] as List;
    return tree
        .map((e) => LearningObjectiveModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }
}
