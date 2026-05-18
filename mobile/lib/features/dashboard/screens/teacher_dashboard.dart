import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../widgets/info_card.dart';
import '../../../widgets/role_badge.dart';
import '../widgets/app_drawer.dart';

class TeacherDashboard extends StatelessWidget {
  final UserModel user;

  const TeacherDashboard({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: const Text('Teacher Dashboard',
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
                gradient: const LinearGradient(
                  colors: [Color(0xFF1E3A5F), Color(0xFF2563EB)],
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Hello, ${user.name.split(' ').first}!',
                            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        const Text('Ready for today\'s classes?',
                            style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 13)),
                        const SizedBox(height: 12),
                        RoleBadge(role: user.role, large: true),
                      ],
                    ),
                  ),
                  const Icon(Icons.menu_book_outlined, color: Color(0x40FFFFFF), size: 64),
                ],
              ),
            ),
            const SizedBox(height: 20),

            const Text('My Classes', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),

            ...[
              ['Mathematics 10A', 'Mon, Wed, Fri — 08:00', Colors.blue],
              ['Physics 11B', 'Tue, Thu — 10:00', Colors.indigo],
              ['Mathematics 9C', 'Mon, Wed — 13:00', Colors.cyan],
            ].map((c) => Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: InfoCard(
                title: c[0] as String,
                subtitle: c[1] as String,
                icon: Icons.class_outlined,
                iconColor: c[2] as Color,
                iconBg: (c[2] as Color).withOpacity(0.1),
              ),
            )),

            const SizedBox(height: 20),
            const Text('Quick Access', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),

            InfoCard(
              title: 'Attendance Entry',
              subtitle: 'Mark today\'s class attendance',
              icon: Icons.check_circle_outline,
              iconColor: AppColors.accent,
              iconBg: const Color(0xFFECFDF5),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
            const SizedBox(height: 10),
            InfoCard(
              title: 'Grade Entry',
              subtitle: 'Enter and manage student grades',
              icon: Icons.star_outline,
              iconColor: Colors.amber,
              iconBg: const Color(0xFFFFFBEB),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
            const SizedBox(height: 10),
            InfoCard(
              title: 'Reports',
              subtitle: 'View class performance reports',
              icon: Icons.bar_chart,
              iconColor: Colors.purple,
              iconBg: const Color(0xFFFAF5FF),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
          ],
        ),
      ),
    );
  }
}
