import '../../../core/models/attendance_model.dart';
import '../../../core/services/api_service.dart';

class AttendanceRepository {
  final ApiService _apiService;

  const AttendanceRepository({required ApiService apiService})
      : _apiService = apiService;

  Future<AttendancePage> fetchAttendance({
    required int studentId,
    DateTime? dateFrom,
    DateTime? dateTo,
  }) async {
    final response = await _apiService.dio.get(
      '/v1/attendance',
      queryParameters: {
        'student_id': studentId,
        if (dateFrom != null) 'date_from': _formatDate(dateFrom),
        if (dateTo != null) 'date_to': _formatDate(dateTo),
      },
    );

    return AttendancePage.fromJson(response.data as Map<String, dynamic>);
  }

  String _formatDate(DateTime date) =>
      '${date.year.toString().padLeft(4, '0')}-'
      '${date.month.toString().padLeft(2, '0')}-'
      '${date.day.toString().padLeft(2, '0')}';
}
