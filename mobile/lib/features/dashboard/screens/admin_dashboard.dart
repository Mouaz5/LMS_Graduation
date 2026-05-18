import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../widgets/info_card.dart';
import '../../../widgets/role_badge.dart';
import '../widgets/app_drawer.dart';

class AdminDashboard extends StatelessWidget {
  final UserModel user;

  const AdminDashboard({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: _buildAppBar(context),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildWelcomeBanner(),
            const SizedBox(height: 20),
            _buildSectionTitle('System Overview'),
            const SizedBox(height: 12),
            _buildStatsGrid(),
            const SizedBox(height: 20),
            _buildSectionTitle('Quick Actions'),
            const SizedBox(height: 12),
            _buildQuickActions(),
          ],
        ),
      ),
    );
  }

  AppBar _buildAppBar(BuildContext context) {
    return AppBar(
      backgroundColor: AppColors.surface,
      elevation: 0,
      surfaceTintColor: Colors.transparent,
      title: const Text('Admin Dashboard',
          style: TextStyle(color: AppColors.textPrimary, fontSize: 17, fontWeight: FontWeight.w700)),
      leading: Builder(
        builder: (ctx) => IconButton(
          icon: const Icon(Icons.menu, color: AppColors.textSecondary),
          onPressed: () => Scaffold.of(ctx).openDrawer(),
        ),
      ),
      actions: [
        Container(
          margin: const EdgeInsets.only(right: 16),
          child: CircleAvatar(
            radius: 17,
            backgroundColor: AppColors.primary,
            child: Text(user.initials,
                style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w700)),
          ),
        ),
      ],
    );
  }

  Widget _buildWelcomeBanner() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1E1B4B), Color(0xFF4338CA)],
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Welcome, ${user.name.split(' ').first}!',
                    style: const TextStyle(
                        color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                const SizedBox(height: 4),
                const Text('Here\'s your school at a glance.',
                    style: TextStyle(color: Color(0xFFA5B4FC), fontSize: 13)),
                const SizedBox(height: 12),
                RoleBadge(role: user.role, large: true),
              ],
            ),
          ),
          const Icon(Icons.admin_panel_settings_outlined, color: Color(0x40FFFFFF), size: 64),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title,
        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary));
  }

  Widget _buildStatsGrid() {
    final stats = [
      {'label': 'Total Users', 'value': '—', 'icon': Icons.people_outline, 'color': AppColors.primary},
      {'label': 'Teachers', 'value': '—', 'icon': Icons.person_outline, 'color': AppColors.accent},
      {'label': 'Students', 'value': '—', 'icon': Icons.school_outlined, 'color': Colors.orange},
      {'label': 'Parents', 'value': '—', 'icon': Icons.family_restroom, 'color': Colors.purple},
    ];

    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 1.6,
      children: stats.map((s) => _buildStatCard(s)).toList(),
    );
  }

  Widget _buildStatCard(Map<String, dynamic> s) {
    final color = s['color'] as Color;
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8)],
      ),
      child: Row(
        children: [
          Container(
            width: 40, height: 40,
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(s['icon'] as IconData, color: color, size: 20),
          ),
          const SizedBox(width: 10),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(s['value'] as String,
                  style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.textPrimary)),
              Text(s['label'] as String,
                  style: const TextStyle(fontSize: 11.5, color: AppColors.textMuted, fontWeight: FontWeight.w500)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Column(
      children: [
        InfoCard(
          title: 'Manage Users',
          subtitle: 'View and manage all system users',
          icon: Icons.people_outline,
          iconColor: AppColors.primary,
          iconBg: const Color(0xFFEEF2FF),
          trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
        ),
        const SizedBox(height: 10),
        InfoCard(
          title: 'System Settings',
          subtitle: 'Configure school-wide settings',
          icon: Icons.settings_outlined,
          iconColor: Colors.purple,
          iconBg: const Color(0xFFFAF5FF),
          trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
        ),
        const SizedBox(height: 10),
        InfoCard(
          title: 'Reports & Analytics',
          subtitle: 'View detailed school reports',
          icon: Icons.bar_chart,
          iconColor: Colors.orange,
          iconBg: const Color(0xFFFFFBEB),
          trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textMuted),
        ),
      ],
    );
  }
}
