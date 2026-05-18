class ClassroomModel {
  final int id;
  final int gradeId;
  final String name;
  final int capacity;
  final String? gradeName;
  final int studentCount;

  ClassroomModel({
    required this.id,
    required this.gradeId,
    required this.name,
    required this.capacity,
    this.gradeName,
    this.studentCount = 0,
  });

  factory ClassroomModel.fromJson(Map<String, dynamic> json) {
    return ClassroomModel(
      id: json['id'] as int,
      gradeId: json['grade_id'] as int,
      name: json['name'] as String,
      capacity: json['capacity'] as int? ?? 30,
      gradeName: json['grade']?['name'] as String?,
      studentCount: (json['student_profiles'] as List?)?.length ?? 0,
    );
  }
}
