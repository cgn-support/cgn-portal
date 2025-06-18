<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Report;
use Carbon\Carbon;
use Livewire\Component;

class ProjectReports extends Component
{
    public Project $project;
    public int $selectedYear;
    public array $availableYears = [];

    public function mount(Project $project)
    {
        $this->project = $project;
        
        // Get available years from published reports
        $this->availableYears = $this->getAvailableYears();
        
        // Default to current year or latest available year
        $this->selectedYear = !empty($this->availableYears) 
            ? (in_array(now()->year, $this->availableYears) ? now()->year : max($this->availableYears))
            : now()->year;
    }

    public function updatedSelectedYear()
    {
        // This will trigger a re-render when year changes
    }

    public function getAvailableYears(): array
    {
        return Report::where('project_id', $this->project->id)
            ->where('status', 'sent') // Only published reports
            ->selectRaw('YEAR(report_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public function getReportsForYear(): array
    {
        $projectStartDate = Carbon::parse($this->project->project_start_date);
        $startYear = $projectStartDate->year;
        $startMonth = $projectStartDate->month;
        
        // Get all reports for this project (not just this year)
        $allReports = Report::where('project_id', $this->project->id)
            ->where('status', 'sent') // Only published reports
            ->get()
            ->keyBy(function($report) {
                return $report->report_year . '-' . $report->report_month;
            });

        $sequentialReports = [];
        
        // Calculate the starting report number for this year
        if ($this->selectedYear == $startYear) {
            // First year - start from report 1
            $startingReportNumber = 1;
            $startingMonth = $startMonth;
            $startingYear = $startYear;
        } else {
            // Calculate offset for subsequent years
            $monthsInFirstYear = 12 - $startMonth + 1; // Months in first year
            $completeYears = $this->selectedYear - $startYear - 1; // Complete years between
            $startingReportNumber = $monthsInFirstYear + ($completeYears * 12) + 1;
            $startingMonth = 1;
            $startingYear = $this->selectedYear;
        }
        
        // Generate 12 sequential reports for this "year view"
        $currentMonth = $startingMonth;
        $currentYear = $startingYear;
        
        for ($i = 0; $i < 12; $i++) {
            $reportNumber = $startingReportNumber + $i;
            $monthDate = Carbon::create($currentYear, $currentMonth, 1);
            $monthName = $monthDate->format('F Y');
            
            // Look for report in our collection
            $reportKey = $currentYear . '-' . $currentMonth;
            $report = $allReports->get($reportKey);
            
            $sequentialReports[] = [
                'report_number' => $reportNumber,
                'month' => $currentMonth,
                'year' => $currentYear,
                'name' => $monthName,
                'short_name' => $monthDate->format('M Y'),
                'report' => $report, // Will be null if no report exists
                'is_current_month' => $monthDate->isSameMonth(now()),
                'is_future_month' => $monthDate->isFuture()
            ];
            
            // Move to next month
            $currentMonth++;
            if ($currentMonth > 12) {
                $currentMonth = 1;
                $currentYear++;
            }
        }

        return $sequentialReports;
    }

    public function render()
    {
        return view('livewire.project-reports', [
            'monthlyReports' => $this->getReportsForYear(),
            'availableYears' => $this->availableYears,
        ]);
    }
}
