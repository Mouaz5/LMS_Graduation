import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../features/auth/bloc/auth_bloc.dart';
import '../../../features/auth/bloc/auth_event.dart';
import '../../../features/auth/bloc/auth_state.dart';
import '../../../widgets/role_badge.dart';

class _NavItem {
  final String label;
  final IconData icon;
  final String permission;
  final bool alwaysVisible;

  const _NavItem({
    required this.label,
    required this.icon,
    required this.permission,
    this.alwaysVisible = false,
  });
}

const _navItems = [
  _NavItem(label: 'Dashboard', icon: Icons.home_outlined, permission: '', alwaysVisible: true),
  _NavItem(label: 'Users', icon: Icons.people_outline, permission: 'view_users'),
  _NavItem(label: 'Students', icon: Icons.school_outlined, permission: 'view_students'),
  _NavItem(label: 'Grades', icon: Icons.star_outline, permission: 'view_grades'),
  _NavItem(label: 'Attendance', icon: Icons.check_circle_outline, permission: 'view_attendance'),
  _NavItem(label: 'Reports', icon: Icons.bar_chart, permission: 'view_reports'),
  _NavItem(label: 'Fees', icon: Icons.payment_outlined, permission: 'view_fees'),
  _NavItem(label: 'Transport', icon: Icons.directions_bus_outlined, permission: 'view_transport'),
  _NavItem(label: 'Settings', icon: Icons.settings_outlined, permission: 'manage_settings'),
];

class AppDrawer extends StatelessWidget {
  const AppDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthBloc, AuthState>(
      builder: (context, state) {
        if (state is! AuthAuthenticated) return const SizedBox.shrink();
        final user = state.user;
        final permissions = state.permissions;

        final visibleItems = _navItems
            .where((item) => item.alwaysVisible || permissions.contains(item.permission))
            .toList();

        return Drawer(
          backgroundColor: AppColors.sidebarBg,
          child: SafeArea(
            child: Column(
              children: [
                // Header
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    border: Border(
                      bottom: BorderSide(color: Color(0x33818CF8)),
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 42,
                        height: 42,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [AppColors.primaryLight, AppColors.primary],
                          ),
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.primary.withOpacity(0.4),
                              blurRadius: 12,
                            ),
                          ],
                        ),
                        child: const Icon(Icons.school_rounded, color: Colors.white, size: 22),
                      ),
                      const SizedBox(width: 12),
                      const Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('SchoolLMS',
                              style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 17,
                                  fontWeight: FontWeight.w800)),
                          Text('Management System',
                              style: TextStyle(
                                  color: AppColors.sidebarText, fontSize: 10, letterSpacing: 1)),
                        ],
                      ),
                    ],
                  ),
                ),

                // User info
                Container(
                  margin: const EdgeInsets.all(12),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      _buildAvatar(user),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(user.name,
                                style: const TextStyle(
                                    color: Colors.white, fontSize: 13.5, fontWeight: FontWeight.w600),
                                overflow: TextOverflow.ellipsis),
                            const SizedBox(height: 4),
                            RoleBadge(role: user.role),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

                // Nav items
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    itemCount: visibleItems.length,
                    itemBuilder: (context, index) {
                      final item = visibleItems[index];
                      final isFirst = index == 0;
                      return _DrawerNavItem(item: item, isActive: isFirst);
                    },
                  ),
                ),

                // Logout
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: const BoxDecoration(
                    border: Border(top: BorderSide(color: Color(0x33818CF8))),
                  ),
                  child: ListTile(
                    leading: const Icon(Icons.logout, color: Color(0xFFFCA5A5), size: 20),
                    title: const Text('Sign Out',
                        style: TextStyle(color: Color(0xFFFCA5A5), fontSize: 14, fontWeight: FontWeight.w600)),
                    onTap: () {
                      Navigator.pop(context);
                      context.read<AuthBloc>().add(const AuthLogoutRequested());
                    },
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    hoverColor: Colors.red.withOpacity(0.1),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildAvatar(UserModel user) {
    return Container(
      width: 38,
      height: 38,
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [
          AppColors.roleColor(user.role).withOpacity(0.8),
          AppColors.roleColor(user.role),
        ]),
        shape: BoxShape.circle,
      ),
      child: Center(
        child: Text(user.initials,
            style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w700)),
      ),
    );
  }
}

class _DrawerNavItem extends StatelessWidget {
  final _NavItem item;
  final bool isActive;

  const _DrawerNavItem({required this.item, required this.isActive});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 2),
      decoration: isActive
          ? BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(10),
              boxShadow: [
                BoxShadow(color: AppColors.primary.withOpacity(0.4), blurRadius: 8),
              ],
            )
          : null,
      child: ListTile(
        dense: true,
        leading: Icon(item.icon, size: 20, color: isActive ? Colors.white : AppColors.sidebarText),
        title: Text(
          item.label,
          style: TextStyle(
            fontSize: 13.5,
            fontWeight: FontWeight.w500,
            color: isActive ? Colors.white : AppColors.sidebarText,
          ),
        ),
        onTap: () => Navigator.pop(context),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        hoverColor: Colors.white.withOpacity(0.05),
      ),
    );
  }
}
