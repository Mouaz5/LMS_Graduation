import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

class InfoCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color? iconColor;
  final Color? iconBg;
  final Widget? trailing;
  final VoidCallback? onTap;
  final List<Widget>? children;

  const InfoCard({
    super.key,
    required this.title,
    required this.subtitle,
    required this.icon,
    this.iconColor,
    this.iconBg,
    this.trailing,
    this.onTap,
    this.children,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          ListTile(
            onTap: onTap,
            contentPadding: const EdgeInsets.all(16),
            leading: Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: iconBg ?? AppColors.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: iconColor ?? AppColors.primary, size: 22),
            ),
            title: Text(
              title,
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
            subtitle: Text(
              subtitle,
              style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
            ),
            trailing: trailing,
          ),
          if (children != null) ...children!,
        ],
      ),
    );
  }
}
