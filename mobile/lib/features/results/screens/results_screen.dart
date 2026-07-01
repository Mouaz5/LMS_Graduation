import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/grade_model.dart';
import '../../../core/models/report_card_model.dart';
import '../../../core/services/api_service.dart';
import '../bloc/results_bloc.dart';
import '../bloc/results_event.dart';
import '../bloc/results_state.dart';
import '../data/results_repository.dart';

class ResultsScreen extends StatelessWidget {
  final int studentId;

  const ResultsScreen({super.key, required this.studentId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => ResultsBloc(
        repository: ResultsRepository(apiService: context.read<ApiService>()),
        studentId: studentId,
      )..add(const ResultsLoadRequested()),
      child: const _ResultsView(),
    );
  }
}

class _ResultsView extends StatelessWidget {
  const _ResultsView();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      appBar: AppBar(
        title: const Text('My Results'),
        actions: [
          BlocBuilder<ResultsBloc, ResultsState>(
            builder: (context, state) {
              if (state is! ResultsLoaded || state.selectedSemesterId == null) {
                return const SizedBox.shrink();
              }
              return IconButton(
                icon: state.isDownloadingPdf
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : const Icon(Icons.download_outlined),
                onPressed: state.isDownloadingPdf
                    ? null
                    : () => context
                        .read<ResultsBloc>()
                        .add(const ResultsPdfDownloadRequested()),
              );
            },
          ),
        ],
      ),
      body: BlocConsumer<ResultsBloc, ResultsState>(
        listener: (context, state) {
          if (state is ResultsError) {
            ScaffoldMessenger.of(context)
                .showSnackBar(SnackBar(content: Text(state.message)));
          }
        },
        builder: (context, state) {
          if (state is ResultsInitial || state is ResultsLoading) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            );
          }

          if (state is ResultsError) {
            return _ErrorView(
              message: state.message,
              onRetry: () => context
                  .read<ResultsBloc>()
                  .add(const ResultsLoadRequested()),
            );
          }

          final loaded = state as ResultsLoaded;

          return Column(
            children: [
              if (loaded.data.semesters.isNotEmpty)
                _SemesterSelector(
                  semesters: loaded.data.semesters,
                  selectedId: loaded.selectedSemesterId,
                  onChanged: (id) => context
                      .read<ResultsBloc>()
                      .add(ResultsSemesterChanged(id)),
                ),
              Expanded(
                child: loaded.data.summaries.isEmpty
                    ? const _EmptyResults()
                    : LayoutBuilder(
                        builder: (context, constraints) {
                          final columns = (constraints.maxWidth / 340)
                              .floor()
                              .clamp(1, 4);
                          return GridView.builder(
                            padding: const EdgeInsets.all(16),
                            gridDelegate:
                                SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: columns,
                              mainAxisSpacing: 12,
                              crossAxisSpacing: 12,
                              childAspectRatio: 1.3,
                            ),
                            itemCount: loaded.data.summaries.length,
                            itemBuilder: (context, i) => _SubjectCard(
                              summary: loaded.data.summaries[i],
                              grades: loaded.data
                                      .gradesBySubject[loaded.data.summaries[i].subjectId] ??
                                  const [],
                            ),
                          );
                        },
                      ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _SemesterSelector extends StatelessWidget {
  final List<SemesterOption> semesters;
  final int? selectedId;
  final ValueChanged<int> onChanged;

  const _SemesterSelector({
    required this.semesters,
    required this.selectedId,
    required this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      color: AppColors.surface,
      padding: const EdgeInsets.all(16),
      child: DropdownButtonFormField<int>(
        initialValue: selectedId,
        decoration: InputDecoration(
          labelText: 'Semester',
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        ),
        items: semesters
            .map((s) => DropdownMenuItem(value: s.id, child: Text(s.label)))
            .toList(),
        onChanged: (value) {
          if (value != null) onChanged(value);
        },
      ),
    );
  }
}

class _SubjectCard extends StatelessWidget {
  final GradeSummaryModel summary;
  final List<GradeModel> grades;

  const _SubjectCard({required this.summary, required this.grades});

  @override
  Widget build(BuildContext context) {
    final gradeColors = _gradeColors(summary.letterGrade);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  summary.subjectName ?? 'Subject',
                  style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: gradeColors.$1,
                  shape: BoxShape.circle,
                ),
                alignment: Alignment.center,
                child: Text(summary.letterGrade,
                    style: TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 14,
                        color: gradeColors.$2)),
              ),
            ],
          ),
          const SizedBox(height: 10),
          ...grades.map((g) => Padding(
                padding: const EdgeInsets.symmetric(vertical: 3),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        g.examTypeName ?? 'Exam',
                        style: const TextStyle(
                            fontSize: 12, color: AppColors.textSecondary),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Text('${g.score.toStringAsFixed(0)}/${g.maxScore.toStringAsFixed(0)}',
                        style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary)),
                  ],
                ),
              )),
          const Spacer(),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: (summary.weightedAverage / 100).clamp(0, 1),
              minHeight: 6,
              backgroundColor: AppColors.border,
              valueColor: AlwaysStoppedAnimation(gradeColors.$2),
            ),
          ),
          const SizedBox(height: 6),
          Text('${summary.weightedAverage.toStringAsFixed(1)}%',
              style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: gradeColors.$2)),
        ],
      ),
    );
  }

  (Color, Color) _gradeColors(String grade) => switch (grade[0].toUpperCase()) {
        'A' => (const Color(0xFFDCFCE7), const Color(0xFF166534)),
        'B' => (const Color(0xFFDBEAFE), const Color(0xFF1E40AF)),
        'C' => (const Color(0xFFFEF9C3), const Color(0xFF854D0E)),
        'D' => (const Color(0xFFFED7AA), const Color(0xFF9A3412)),
        _ => (const Color(0xFFFEE2E2), const Color(0xFF991B1B)),
      };
}

class _EmptyResults extends StatelessWidget {
  const _EmptyResults();

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.bar_chart_outlined,
              size: 52, color: AppColors.textMuted.withOpacity(0.4)),
          const SizedBox(height: 12),
          const Text('No results yet',
              style: TextStyle(color: AppColors.textMuted, fontSize: 14)),
          const SizedBox(height: 6),
          const Text('No grades have been entered for this semester.',
              style: TextStyle(color: AppColors.textMuted, fontSize: 12)),
        ],
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
