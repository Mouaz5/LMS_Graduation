import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

class AppButton extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final bool isLoading;
  final bool outlined;
  final Color? color;
  final IconData? icon;
  final double? width;

  const AppButton({
    super.key,
    required this.label,
    this.onPressed,
    this.isLoading = false,
    this.outlined = false,
    this.color,
    this.icon,
    this.width,
  });

  @override
  Widget build(BuildContext context) {
    final bg = color ?? AppColors.primary;

    return SizedBox(
      width: width,
      height: 50,
      child: outlined
          ? OutlinedButton(
              onPressed: isLoading ? null : onPressed,
              style: OutlinedButton.styleFrom(
                side: BorderSide(color: bg),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: _buildChild(bg),
            )
          : ElevatedButton(
              onPressed: isLoading ? null : onPressed,
              style: ElevatedButton.styleFrom(
                backgroundColor: bg,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                elevation: 0,
                shadowColor: Colors.transparent,
              ),
              child: _buildChild(Colors.white),
            ),
    );
  }

  Widget _buildChild(Color fgColor) {
    if (isLoading) {
      return SizedBox(
        width: 20,
        height: 20,
        child: CircularProgressIndicator(strokeWidth: 2, color: fgColor),
      );
    }
    if (icon != null) {
      return Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 18, color: fgColor),
          const SizedBox(width: 8),
          Text(label, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 15, color: fgColor)),
        ],
      );
    }
    return Text(label, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 15, color: fgColor));
  }
}
