import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

class RoleBadge extends StatelessWidget {
  final String role;
  final bool large;

  const RoleBadge({super.key, required this.role, this.large = false});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: large ? 14 : 10,
        vertical: large ? 6 : 3,
      ),
      decoration: BoxDecoration(
        color: AppColors.roleBg(role),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 5,
            height: 5,
            decoration: BoxDecoration(
              color: AppColors.roleColor(role),
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 5),
          Text(
            role[0].toUpperCase() + role.substring(1),
            style: TextStyle(
              fontSize: large ? 13 : 11.5,
              fontWeight: FontWeight.w600,
              color: AppColors.roleColor(role),
            ),
          ),
        ],
      ),
    );
  }
}
