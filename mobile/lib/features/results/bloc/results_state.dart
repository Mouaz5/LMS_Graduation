import 'dart:io';
import 'package:equatable/equatable.dart';
import '../../../core/models/report_card_model.dart';

abstract class ResultsState extends Equatable {
  const ResultsState();
  @override
  List<Object?> get props => [];
}

class ResultsInitial extends ResultsState {
  const ResultsInitial();
}

class ResultsLoading extends ResultsState {
  const ResultsLoading();
}

class ResultsLoaded extends ResultsState {
  final ReportCardData data;
  final int? selectedSemesterId;
  final bool isDownloadingPdf;
  final File? downloadedPdf;

  const ResultsLoaded({
    required this.data,
    this.selectedSemesterId,
    this.isDownloadingPdf = false,
    this.downloadedPdf,
  });

  ResultsLoaded copyWith({
    ReportCardData? data,
    int? selectedSemesterId,
    bool? isDownloadingPdf,
    File? downloadedPdf,
  }) {
    return ResultsLoaded(
      data: data ?? this.data,
      selectedSemesterId: selectedSemesterId ?? this.selectedSemesterId,
      isDownloadingPdf: isDownloadingPdf ?? this.isDownloadingPdf,
      downloadedPdf: downloadedPdf,
    );
  }

  @override
  List<Object?> get props =>
      [data.summaries, selectedSemesterId, isDownloadingPdf, downloadedPdf?.path];
}

class ResultsError extends ResultsState {
  final String message;
  const ResultsError(this.message);
  @override
  List<Object?> get props => [message];
}
