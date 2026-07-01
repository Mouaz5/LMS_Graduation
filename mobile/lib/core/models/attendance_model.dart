class AttendanceModel {
  final int id;
  final int studentUserId;
  final String date;
  final String status;
  final String? subjectName;
  final String? justificationStatus;

  const AttendanceModel({
    required this.id,
    required this.studentUserId,
    required this.date,
    required this.status,
    this.subjectName,
    this.justificationStatus,
  });

  factory AttendanceModel.fromJson(Map<String, dynamic> json) {
    return AttendanceModel(
      id: json['id'] as int,
      studentUserId: json['student_user_id'] as int,
      date: json['date'] as String,
      status: json['status'] as String,
      subjectName: json['schedule_slot']?['subject']?['name'] as String?,
      justificationStatus: json['justification']?['status'] as String?,
    );
  }
}

class AttendancePage {
  final List<AttendanceModel> records;
  final int currentPage;
  final int lastPage;
  final int total;

  const AttendancePage({
    required this.records,
    required this.currentPage,
    required this.lastPage,
    required this.total,
  });

  factory AttendancePage.fromJson(Map<String, dynamic> json) {
    return AttendancePage(
      records: (json['data'] as List)
          .map((e) => AttendanceModel.fromJson(e as Map<String, dynamic>))
          .toList(),
      currentPage: json['current_page'] as int,
      lastPage: json['last_page'] as int,
      total: json['total'] as int,
    );
  }
}
