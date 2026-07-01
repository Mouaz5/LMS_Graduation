import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/learning_objective_model.dart';
import '../../../core/services/api_service.dart';
import '../../diagnostic_test/screens/diagnostic_test_screen.dart';
import '../bloc/knowledge_map_bloc.dart';
import '../bloc/knowledge_map_event.dart';
import '../bloc/knowledge_map_state.dart';
import '../data/knowledge_map_repository.dart';

class KnowledgeMapScreen extends StatelessWidget {
  final int studentId;

  const KnowledgeMapScreen({super.key, required this.studentId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => KnowledgeMapBloc(
        repository:
            KnowledgeMapRepository(apiService: context.read<ApiService>()),
        studentId: studentId,
      )..add(const KnowledgeMapSubjectsLoadRequested()),
      child: _KnowledgeMapView(studentId: studentId),
    );
  }
}

class _KnowledgeMapView extends StatelessWidget {
  final int studentId;

  const _KnowledgeMapView({required this.studentId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      appBar: AppBar(title: const Text('Knowledge Map')),
      body: BlocBuilder<KnowledgeMapBloc, KnowledgeMapState>(
        builder: (context, state) {
          if (state is KnowledgeMapInitial || state is KnowledgeMapLoading) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            );
          }

          if (state is KnowledgeMapError) {
            return _ErrorView(
              message: state.message,
              onRetry: () => context
                  .read<KnowledgeMapBloc>()
                  .add(const KnowledgeMapSubjectsLoadRequested()),
            );
          }

          final loaded = state as KnowledgeMapLoaded;

          if (loaded.subjects.isEmpty) {
            return const _EmptyState(message: 'No subjects available.');
          }

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              DropdownButtonFormField<int>(
                initialValue: loaded.selectedSubjectId,
                decoration: InputDecoration(
                  labelText: 'Subject',
                  filled: true,
                  fillColor: AppColors.surface,
                  border:
                      OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                  contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12, vertical: 8),
                ),
                items: loaded.subjects
                    .map((s) =>
                        DropdownMenuItem(value: s.id, child: Text(s.name)))
                    .toList(),
                onChanged: (value) {
                  if (value != null) {
                    context
                        .read<KnowledgeMapBloc>()
                        .add(KnowledgeMapSubjectSelected(value));
                  }
                },
              ),
              const SizedBox(height: 16),
              _SummaryBar(state: loaded),
              const SizedBox(height: 16),
              if (loaded.tree.isEmpty)
                const _EmptyState(message: 'No learning objectives found for this subject.')
              else
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Column(
                    children: loaded.tree
                        .map((node) => _ObjectiveNode(
                              node: node,
                              depth: 0,
                              expandedNodeIds: loaded.expandedNodeIds,
                            ))
                        .toList(),
                  ),
                ),
              const SizedBox(height: 16),
              const _Legend(),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.quiz_outlined),
                  label: const Text('Take Diagnostic Test'),
                  onPressed: () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) =>
                          DiagnosticTestScreen(studentId: studentId),
                    ),
                  ),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _SummaryBar extends StatelessWidget {
  final KnowledgeMapLoaded state;

  const _SummaryBar({required this.state});

  @override
  Widget build(BuildContext context) {
    final avg = state.averageMastery;
    final stats = [
      ('Mastered', '${state.masteredCount}', const Color(0xFF166534)),
      ('Developing', '${state.developingCount}', const Color(0xFF854D0E)),
      ('Needs Work', '${state.needsWorkCount}', const Color(0xFF991B1B)),
      ('Average', avg != null ? '${avg.toStringAsFixed(0)}%' : '—',
          AppColors.primary),
    ];

    return LayoutBuilder(builder: (context, constraints) {
      final columns = constraints.maxWidth >= 500 ? 4 : 2;
      return GridView.count(
        crossAxisCount: columns,
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        mainAxisSpacing: 10,
        crossAxisSpacing: 10,
        childAspectRatio: 1.6,
        children: stats
            .map((s) => Container(
                  decoration: BoxDecoration(
                    color: s.$3.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: s.$3.withOpacity(0.2)),
                  ),
                  alignment: Alignment.center,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(s.$2,
                          style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.w800,
                              color: s.$3)),
                      Text(s.$1,
                          style: TextStyle(
                              fontSize: 11,
                              color: s.$3,
                              fontWeight: FontWeight.w500)),
                    ],
                  ),
                ))
            .toList(),
      );
    });
  }
}

class _ObjectiveNode extends StatelessWidget {
  final LearningObjectiveModel node;
  final int depth;
  final Set<int> expandedNodeIds;

  const _ObjectiveNode({
    required this.node,
    required this.depth,
    required this.expandedNodeIds,
  });

  @override
  Widget build(BuildContext context) {
    final hasChildren = node.children.isNotEmpty;
    final isExpanded = expandedNodeIds.contains(node.id);
    final colors = _levelColors(node.level);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        InkWell(
          onTap: hasChildren
              ? () => context
                  .read<KnowledgeMapBloc>()
                  .add(KnowledgeMapNodeToggled(node.id))
              : null,
          borderRadius: BorderRadius.circular(10),
          child: Padding(
            padding: EdgeInsets.only(
                left: 12.0 * depth + 8, right: 8, top: 10, bottom: 10),
            child: Row(
              children: [
                if (hasChildren)
                  Icon(
                    isExpanded ? Icons.expand_more : Icons.chevron_right,
                    size: 20,
                    color: AppColors.textMuted,
                  )
                else
                  const SizedBox(width: 20),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    node.name,
                    style: const TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary),
                  ),
                ),
                Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(color: colors.$1, shape: BoxShape.circle),
                  alignment: Alignment.center,
                  child: Text(
                    node.masteryPercent != null
                        ? '${node.masteryPercent!.toStringAsFixed(0)}%'
                        : '—',
                    style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: colors.$2),
                  ),
                ),
              ],
            ),
          ),
        ),
        if (hasChildren && isExpanded)
          ...node.children.map((child) => _ObjectiveNode(
                node: child,
                depth: depth + 1,
                expandedNodeIds: expandedNodeIds,
              )),
        const Divider(height: 1, color: AppColors.border),
      ],
    );
  }

  (Color, Color) _levelColors(MasteryLevel level) => switch (level) {
        MasteryLevel.mastered => (const Color(0xFFDCFCE7), const Color(0xFF166534)),
        MasteryLevel.developing => (const Color(0xFFFEF9C3), const Color(0xFF854D0E)),
        MasteryLevel.needsWork => (const Color(0xFFFEE2E2), const Color(0xFF991B1B)),
        MasteryLevel.notStarted => (const Color(0xFFF1F5F9), const Color(0xFF94A3B8)),
      };
}

class _Legend extends StatelessWidget {
  const _Legend();

  @override
  Widget build(BuildContext context) {
    final items = [
      ('Mastered (≥70%)', const Color(0xFF166534)),
      ('Developing (40–69%)', const Color(0xFF854D0E)),
      ('Needs Work (<40%)', const Color(0xFF991B1B)),
      ('Not Assessed', const Color(0xFF94A3B8)),
    ];

    return Wrap(
      spacing: 14,
      runSpacing: 8,
      children: items
          .map((i) => Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 10,
                    height: 10,
                    decoration: BoxDecoration(color: i.$2, shape: BoxShape.circle),
                  ),
                  const SizedBox(width: 6),
                  Text(i.$1,
                      style: const TextStyle(
                          fontSize: 11, color: AppColors.textMuted)),
                ],
              ))
          .toList(),
    );
  }
}

class _EmptyState extends StatelessWidget {
  final String message;
  const _EmptyState({required this.message});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 60),
      child: Center(
        child: Column(
          children: [
            Icon(Icons.account_tree_outlined,
                size: 52, color: AppColors.textMuted.withOpacity(0.4)),
            const SizedBox(height: 12),
            Text(message,
                style: const TextStyle(color: AppColors.textMuted, fontSize: 14)),
          ],
        ),
      ),
    );
  }
}

class _ErrorView extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;

  const _ErrorView({required this.message, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline,
                size: 52, color: AppColors.textMuted),
            const SizedBox(height: 12),
            Text(message,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.textSecondary)),
            const SizedBox(height: 20),
            ElevatedButton(onPressed: onRetry, child: const Text('Retry')),
          ],
        ),
      ),
    );
  }
}
