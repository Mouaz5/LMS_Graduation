class ScheduleSlotModel {
  final int id;
  final int classroomId;
  final int subjectId;
  final int teacherUserId;
  final String dayOfWeek;
  final int periodNumber;
  final String startTime;
  final String endTime;
  final int semesterId;
  final String? subjectName;
  final String? classroomName;
  final String? gradeName;
  final String? semesterName;
  final String? teacherName;

  const ScheduleSlotModel({
    required this.id,
    required this.classroomId,
    required this.subjectId,
    required this.teacherUserId,
    required this.dayOfWeek,
    required this.periodNumber,
    required this.startTime,
    required this.endTime,
    required this.semesterId,
    this.subjectName,
    this.classroomName,
    this.gradeName,
    this.semesterName,
    this.teacherName,
  });

  factory ScheduleSlotModel.fromJson(Map<String, dynamic> json) {
    return ScheduleSlotModel(
      id:            json['id'] as int,
      classroomId:   json['classroom_id'] as int,
      subjectId:     json['subject_id'] as int,
      teacherUserId: json['teacher_user_id'] as int,
      dayOfWeek:     json['day_of_week'] as String,
      periodNumber:  json['period_number'] as int,
      startTime:     json['start_time'] as String,
      endTime:       json['end_time'] as String,
      semesterId:    json['semester_id'] as int,
      subjectName:   json['subject']?['name'] as String?,
      classroomName: json['classroom']?['name'] as String?,
      gradeName:     json['classroom']?['grade']?['name'] as String?,
      semesterName:  json['semester']?['name'] as String?,
      teacherName:   json['teacher']?['name'] as String?,
    );
  }
}
