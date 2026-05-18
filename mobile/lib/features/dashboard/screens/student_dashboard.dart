import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../widgets/role_badge.dart';
import '../widgets/app_drawer.dart';

class StudentDashboard extends StatelessWidget {
  final UserModel user;

  const StudentDashboard({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: const Text('Student Dashboard',
            style: TextStyle(color: AppColors.textPrimary, fontSize: 17, fontWeight: FontWeight.w700)),
        leading: Builder(
          builder: (ctx) => IconButton(
            icon: const Icon(Icons.menu, color: AppColors.textSecondary),
            onPressed: () => Scaffold.of(ctx).openDrawer(),
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome banner
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFF064E3B), Color(0xFF059669)]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Hi, ${user.name.split(' ').first}!',
                            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        const Text('Keep up the great work!',
                            style: TextStyle(color: Color(0xFFA7F3D0), fontSize: 13)),
                        const SizedBox(height: 12),
                        RoleBadge(role: user.role, large: true),
                      ],
                    ),
                  ),
                  const Icon(Icons.school_outlined, color: Color(0x40FFFFFF), size: 64),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Grades
            const Text('My Grades', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            Container(
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                children: [
                  ['Mathematics', 'A', Colors.green],
                  ['Physics', 'B+', Colors.blue],
                  ['History', 'A-', Colors.green],
                  ['English', 'B', Colors.blue],
                ].asMap().entries.map((entry) {
                  final i = entry.key;
                  final g = entry.value;
                  return Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 13),
                    decoration: BoxDecoration(
                      border: Border(
                        bottom: BorderSide(color: i < 3 ? AppColors.border : Colors.transparent),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(g[0] as String,
                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: AppColors.textPrimary)),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: (g[2] as Color).withOpacity(0.1),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(g[1] as String,
                              style: TextStyle(
                                  fontSize: 12.5, fontWeight: FontWeight.w700, color: g[2] as Color)),
                        ),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),

            const SizedBox(height: 20),
            const Text('Today\'s Schedule', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            Container(
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                children: [
                  ['08:00', 'Mathematics', AppColors.primary],
                  ['10:00', 'Physics', Colors.blue],
                  ['12:00', 'English', AppColors.accent],
                  ['14:00', 'History', Colors.amber],
                ].asMap().entries.map((entry) {
                  final i = entry.key;
                  final s = entry.value;
                  return Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      border: Border(bottom: BorderSide(color: i < 3 ? AppColors.border : Colors.transparent)),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 8, height: 8,
                          decoration: BoxDecoration(color: s[2] as Color, shape: BoxShape.circle),
                        ),
                        const SizedBox(width: 12),
                        Text(s[0] as String,
                            style: const TextStyle(fontSize: 12, color: AppColors.textMuted, fontWeight: FontWeight.w500)),
                        const SizedBox(width: 12),
                        Text(s[1] as String,
                            style: const TextStyle(fontSize: 14, color: AppColors.textPrimary, fontWeight: FontWeight.w500)),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),

            const SizedBox(height: 20),
            const Text('Attendance', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            Row(
              children: [
                ['18', 'Present', AppColors.accent],
                ['2', 'Absent', AppColors.error],
                ['1', 'Late', Colors.orange],
              ].map((a) => Expanded(
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: (a[2] as Color).withOpacity(0.08),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: (a[2] as Color).withOpacity(0.2)),
                  ),
                  child: Column(
                    children: [
                      Text(a[0] as String,
                          style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: a[2] as Color)),
                      Text(a[1] as String,
                          style: TextStyle(fontSize: 11, color: a[2] as Color, fontWeight: FontWeight.w500)),
                    ],
                  ),
                ),
              )).toList(),
            ),
          ],
        ),
      ),
    );
  }
}
