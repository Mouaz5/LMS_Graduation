class TeacherAssignmentModel {
  final int id;
  final int teacherUserId;
  final int subjectId;
  final int classroomId;
  final int academicYearId;
  final String? subjectName;
  final String? subjectCode;
  final String? classroomName;
  final String? gradeName;

  TeacherAssignmentModel({
    required this.id,
    required this.teacherUserId,
    required this.subjectId,
    required this.classroomId,
    required this.academicYearId,
    this.subjectName,
    this.subjectCode,
    this.classroomName,
    this.gradeName,
  });

  factory TeacherAssignmentModel.fromJson(Map<String, dynamic> json) {
    return TeacherAssignmentModel(
      id: json['id'] as int,
      teacherUserId: json['teacher_user_id'] as int,
      subjectId: json['subject_id'] as int,
      classroomId: json['classroom_id'] as int,
      academicYearId: json['academic_year_id'] as int,
      subjectName: json['subject']?['name'] as String?,
      subjectCode: json['subject']?['code'] as String?,
      classroomName: json['classroom']?['name'] as String?,
      gradeName: json['classroom']?['grade']?['name'] as String?,
    );
  }
}
