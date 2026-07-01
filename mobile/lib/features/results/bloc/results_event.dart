import 'package:equatable/equatable.dart';

abstract class ResultsEvent extends Equatable {
  const ResultsEvent();
  @override
  List<Object?> get props => [];
}

class ResultsLoadRequested extends ResultsEvent {
  const ResultsLoadRequested();
}

class ResultsSemesterChanged extends ResultsEvent {
  final int semesterId;
  const ResultsSemesterChanged(this.semesterId);
  @override
  List<Object?> get props => [semesterId];
}

class ResultsPdfDownloadRequested extends ResultsEvent {
  const ResultsPdfDownloadRequested();
}
