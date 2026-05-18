import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../widgets/info_card.dart';
import '../../../widgets/role_badge.dart';
import '../widgets/app_drawer.dart';

class ParentDashboard extends StatelessWidget {
  final UserModel user;

  const ParentDashboard({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: const Text('Parent Dashboard',
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
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFF4C1D95), Color(0xFF7C3AED)]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Welcome, ${user.name.split(' ').first}!',
                            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        const Text('Track your children\'s progress here.',
                            style: TextStyle(color: Color(0xFFDDD6FE), fontSize: 13)),
                        const SizedBox(height: 12),
                        RoleBadge(role: user.role, large: true),
                      ],
                    ),
                  ),
                  const Icon(Icons.family_restroom, color: Color(0x40FFFFFF), size: 64),
                ],
              ),
            ),
            const SizedBox(height: 20),

            const Text('My Children', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),

            ...[
              ['Ahmad Hassan', 'Grade 10 — Section A', const Color(0xFF7C3AED)],
              ['Sara Hassan', 'Grade 8 — Section B', const Color(0xFFEC4899)],
            ].map((child) => Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppColors.border),
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 22,
                      backgroundColor: child[2] as Color,
                      child: Text(
                        (child[0] as String).split(' ').map((w) => w[0]).take(2).join(),
                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 13),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(child[0] as String,
                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
                        Text(child[1] as String,
                            style: const TextStyle(fontSize: 12, color: AppColors.textMuted)),
                      ],
                    ),
                    const Spacer(),
                    const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
                  ],
                ),
              ),
            )),

            const SizedBox(height: 20),
            const Text('Quick Access', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),

            InfoCard(
              title: 'Recent Grades',
              subtitle: 'View latest academic results',
              icon: Icons.star_outline,
              iconColor: Colors.amber,
              iconBg: const Color(0xFFFFFBEB),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
            const SizedBox(height: 10),
            InfoCard(
              title: 'Attendance Summary',
              subtitle: 'Check attendance records',
              icon: Icons.check_circle_outline,
              iconColor: AppColors.accent,
              iconBg: const Color(0xFFECFDF5),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
            const SizedBox(height: 10),
            InfoCard(
              title: 'Fee Payments',
              subtitle: 'View and manage school fees',
              icon: Icons.payment_outlined,
              iconColor: Colors.blue,
              iconBg: const Color(0xFFEFF6FF),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
            const SizedBox(height: 10),
            InfoCard(
              title: 'Transport',
              subtitle: 'Bus routes and schedules',
              icon: Icons.directions_bus_outlined,
              iconColor: Colors.teal,
              iconBg: const Color(0xFFF0FDFA),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
            ),
          ],
        ),
      ),
    );
  }
}
