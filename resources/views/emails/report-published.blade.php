<x-mail::message>
# 📊 Your {{ $report->report_month_name }} {{ $report->report_year }} Marketing Report is Ready!

Hello {{ $project->clientUser->name ?? 'Valued Client' }},

We're excited to share your latest marketing performance report! This comprehensive analysis covers your marketing activities and results for {{ $report->report_month_name }} {{ $report->report_year }}.

## 🎯 What's Inside Your Report

- **Marketing Metrics & KPIs** - Key performance indicators for your campaigns
- **Month-over-Month Comparisons** - See how you're improving over time
- **Recent Content** - Blog posts published during the report period
- **Project Updates** - Completed tasks and deliverables

@if($report->content)
## 📈 This Month's Highlights

{!! strip_tags($report->content) !!}
@endif

<x-mail::button :url="$reportUrl" color="primary">
View Your Full Report
</x-mail::button>

@if($report->looker_studio_share_link)
<x-mail::button :url="$report->looker_studio_share_link" color="success">
Access Interactive Analytics Dashboard
</x-mail::button>
@endif

## 📞 Questions or Want to Discuss Results?

Our team is here to help you understand your data and plan next steps. Feel free to reach out to your account manager or reply to this email.

---

**Report Details:**
- **Project:** {{ $project->display_name }}
- **Period:** {{ $report->report_month_name }} {{ $report->report_year }}
- **Generated:** {{ $report->created_at->format('F j, Y \a\t g:i A') }}

Thanks for trusting us with your marketing!

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
