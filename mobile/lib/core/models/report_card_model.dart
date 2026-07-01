import 'grade_model.dart';

class SemesterOption {
  final int id;
  final String name;
  final String? academicYearName;

  const SemesterOption({required this.id, required this.name, this.academicYearName});

  String get label =>
      [academicYearName, name].whereType<String>().join(' — ');
}

class ReportCardData {
  final List<GradeSummaryModel> summaries;
  final Map<int, List<GradeModel>> gradesBySubject;
  final List<SemesterOption> semesters;

  const ReportCardData({
    required this.summaries,
    required this.gradesBySubject,
    required this.semesters,
  });

  factory ReportCardData.fromJson(Map<String, dynamic> json) {
    final summariesJson = json['summaries'] as List;
    final summaries = summariesJson
        .map((e) => GradeSummaryModel.fromJson(e as Map<String, dynamic>))
        .toList();

    final semestersById = <int, SemesterOption>{};
    for (final s in summariesJson) {
      final map = s as Map<String, dynamic>;
      final semester = map['semester'] as Map<String, dynamic>?;
      final semesterId = map['semester_id'] as int?;
      if (semester != null && semesterId != null) {
        semestersById[semesterId] = SemesterOption(
          id: semesterId,
          name: semester['name'] as String? ?? '',
          academicYearName: semester['academic_year']?['name'] as String?,
        );
      }
    }

    final gradesRaw = json['grades'];
    final gradesBySubject = <int, List<GradeModel>>{};
    if (gradesRaw is Map<String, dynamic>) {
      for (final entry in gradesRaw.entries) {
        final list = (entry.value as List)
            .map((e) => GradeModel.fromJson(e as Map<String, dynamic>))
            .toList();
        for (final grade in list) {
          gradesBySubject.putIfAbsent(grade.subjectId, () => []).add(grade);
        }
      }
    }

    return ReportCardData(
      summaries: summaries,
      gradesBySubject: gradesBySubject,
      semesters: semestersById.values.toList()..sort((a, b) => b.id.compareTo(a.id)),
    );
  }
}
