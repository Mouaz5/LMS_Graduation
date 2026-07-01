import 'dart:io';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import '../../../core/models/report_card_model.dart';
import '../../../core/services/api_service.dart';

class ResultsRepository {
  final ApiService _apiService;

  const ResultsRepository({required ApiService apiService})
      : _apiService = apiService;

  Future<ReportCardData> fetchReportCard({
    required int studentId,
    int? semesterId,
  }) async {
    final response = await _apiService.dio.get(
      '/v1/students/$studentId/report-card',
      queryParameters: {
        if (semesterId != null) 'semester_id': semesterId,
      },
    );
    return ReportCardData.fromJson(response.data as Map<String, dynamic>);
  }

  Future<File> downloadReportCardPdf({
    required int studentId,
    required int semesterId,
  }) async {
    final response = await _apiService.dio.get<List<int>>(
      '/v1/students/$studentId/report-card/pdf',
      queryParameters: {'semester_id': semesterId},
      options: Options(responseType: ResponseType.bytes),
    );

    final dir = await getTemporaryDirectory();
    final file = File('${dir.path}/report_card_${studentId}_$semesterId.pdf');
    await file.writeAsBytes(response.data!);
    return file;
  }
}
