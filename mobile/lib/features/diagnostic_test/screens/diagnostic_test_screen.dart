import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/diagnostic_question_model.dart';
import '../../../core/services/api_service.dart';
import '../../knowledge_map/screens/knowledge_map_screen.dart';
import '../bloc/diagnostic_test_bloc.dart';
import '../bloc/diagnostic_test_event.dart';
import '../bloc/diagnostic_test_state.dart';
import '../data/diagnostic_test_repository.dart';

class DiagnosticTestScreen extends StatelessWidget {
  final int studentId;

  const DiagnosticTestScreen({super.key, required this.studentId});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => DiagnosticTestBloc(
        repository:
            DiagnosticTestRepository(apiService: context.read<ApiService>()),
      )..add(const DiagnosticSubjectsLoadRequested()),
      child: _DiagnosticTestView(studentId: studentId),
    );
  }
}

class _DiagnosticTestView extends StatelessWidget {
  final int studentId;

  const _DiagnosticTestView({required this.studentId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      appBar: AppBar(title: const Text('Diagnostic Test')),
      body: BlocConsumer<DiagnosticTestBloc, DiagnosticTestState>(
        listener: (context, state) {
          if (state is DiagnosticSubmitted) {
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(
                builder: (_) => KnowledgeMapScreen(studentId: studentId),
              ),
            );
          }
        },
        builder: (context, state) {
          if (state is DiagnosticTestInitial || state is DiagnosticTestLoading) {
            return const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            );
          }

          if (state is DiagnosticTestError) {
            return _ErrorView(
              message: state.message,
              onRetry: () => context
                  .read<DiagnosticTestBloc>()
                  .add(const DiagnosticSubjectsLoadRequested()),
            );
          }

          if (state is DiagnosticSubjectPicking) {
            return _SubjectPickerView(state: state);
          }

          if (state is DiagnosticInProgress) {
            return _QuestionsView(state: state);
          }

          return const SizedBox.shrink();
        },
      ),
    );
  }
}

class _SubjectPickerView extends StatelessWidget {
  final DiagnosticSubjectPicking state;

  const _SubjectPickerView({required this.state});

  @override
  Widget build(BuildContext context) {
    if (state.subjects.isEmpty) {
      return const Center(
        child: Text('No subjects available.',
            style: TextStyle(color: AppColors.textMuted)),
      );
    }

    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Text('Choose a subject to begin',
              style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary)),
          const SizedBox(height: 16),
          DropdownButtonFormField<int>(
            initialValue: state.selectedSubjectId,
            decoration: InputDecoration(
              labelText: 'Subject',
              filled: true,
              fillColor: AppColors.surface,
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
            ),
            items: state.subjects
                .map((s) => DropdownMenuItem(value: s.id, child: Text(s.name)))
                .toList(),
            onChanged: (value) {
              if (value != null) {
                context
                    .read<DiagnosticTestBloc>()
                    .add(DiagnosticSubjectSelected(value));
              }
            },
          ),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: state.selectedSubjectId == null
                ? null
                : () => context
                    .read<DiagnosticTestBloc>()
                    .add(const DiagnosticTestStartRequested()),
            child: const Padding(
              padding: EdgeInsets.symmetric(vertical: 12),
              child: Text('Start New Test'),
            ),
          ),
        ],
      ),
    );
  }
}

class _QuestionsView extends StatelessWidget {
  final DiagnosticInProgress state;

  const _QuestionsView({required this.state});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('${state.answeredCount} / ${state.totalCount} answered',
                  style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textSecondary)),
              const SizedBox(height: 8),
              ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: LinearProgressIndicator(
                  value: state.totalCount == 0
                      ? 0
                      : state.answeredCount / state.totalCount,
                  minHeight: 8,
                  backgroundColor: AppColors.border,
                  valueColor:
                      const AlwaysStoppedAnimation(AppColors.primary),
                ),
              ),
            ],
          ),
        ),
        Expanded(
          child: ListView.separated(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            itemCount: state.questions.length,
            separatorBuilder: (_, __) => const SizedBox(height: 12),
            itemBuilder: (context, i) => _QuestionCard(
              index: i,
              question: state.questions[i],
              selectedOptionId: state.answers[state.questions[i].id],
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.all(16),
          child: SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: state.isSubmitting || !state.allAnswered
                  ? null
                  : () => context
                      .read<DiagnosticTestBloc>()
                      .add(const DiagnosticSubmitRequested()),
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 12),
                child: state.isSubmitting
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white),
                      )
                    : Text(state.allAnswered
                        ? 'Submit Answers & See Knowledge Map'
                        : 'Answer all questions to submit'),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _QuestionCard extends StatelessWidget {
  final int index;
  final DiagnosticQuestionModel question;
  final int? selectedOptionId;

  const _QuestionCard({
    required this.index,
    required this.question,
    required this.selectedOptionId,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Q${index + 1}. ${question.questionText}',
              style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary)),
          const SizedBox(height: 10),
          ...question.options.map((option) {
            final isSelected = selectedOptionId == option.id;
            return Padding(
              padding: const EdgeInsets.only(bottom: 6),
              child: InkWell(
                onTap: () => context
                    .read<DiagnosticTestBloc>()
                    .add(DiagnosticAnswerSelected(question.id, option.id)),
                borderRadius: BorderRadius.circular(10),
                child: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? AppColors.primary.withOpacity(0.08)
                        : AppColors.surface2,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(
                      color: isSelected ? AppColors.primary : AppColors.border,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        isSelected
                            ? Icons.radio_button_checked
                            : Icons.radio_button_unchecked,
                        size: 18,
                        color: isSelected
                            ? AppColors.primary
                            : AppColors.textMuted,
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(option.optionText,
                            style: TextStyle(
                                fontSize: 13,
                                color: isSelected
                                    ? AppColors.textPrimary
                                    : AppColors.textSecondary)),
                      ),
                    ],
                  ),
                ),
              ),
            );
          }),
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
