@extends('layouts.skeleton')

{{-- Temp Fix for Bootstrap Tabs: Issue #26 --}}
@push('scripts')
  <script type="text/javascript">
      $( ".nav-item" ).click(function() {
          $(this).children().addClass('active');
          $(this).siblings().children().removeClass('active');
      });
  </script>
@endpush


@section('content')
  <div class="dashboard">

    <!-- Page content -->
    <div class="main-content">

      <div class="{{ Auth::user()->getFluidLayout() }}">

        <div class="row">
          <div class="col-xs-9">

          </div>
        </div>

        <div class="row">

          <div class="col-xs-12 col-md-9">

            <div class="dashboard-box dashboard-stat">
              <h2>{{ trans('dashboard.statistics_title') }}</h2>
              <ul class="horizontal">
                <li>
                  <span class="stat-number">{{ $number_of_contacts }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_contacts') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_kids }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_kids') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_reminders }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_reminders') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_notes }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_notes') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_activities }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_activities') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_gifts }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_gifts') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ $number_of_tasks }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_tasks') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ MoneyHelper::format($debt_owed) }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_deb_owed') }}</span>
                </li>
                <li>
                  <span class="stat-number">{{ MoneyHelper::format($debt_due) }}</span>
                  <span class="stat-description">{{ trans('dashboard.statistics_debt_due') }}</span>
                </li>
              </ul>
            </div>

            <!--
            % contacts with significant other
            % contacts with kids -->

            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#coming" role="tab">{{ trans('dashboard.tab_whats_coming') }}</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#actions" role="tab">{{ trans('dashboard.tab_lastest_actions') }}</a>
              </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="coming" role="tabpanel">

                {{-- REMINDERS --}}
                <div class="reminders dashboard-section">
                  <img src="/img/people/reminders.svg" class="section-icon">
                  <h3>{{ trans('dashboard.reminders_title') }}</h3>

                  @if ($upcomingReminders->count() != 0)
                  <ul>
                    @foreach ($upcomingReminders as $reminder)
                      <li>
                        <span class="reminder-in-days">
                          <?php $reminder_day_diff = $reminder->next_expected_date->diffInDays(Carbon\Carbon::now()) + 1 ?>
                          {{ trans_choice('dashboard.reminders_in_days', $reminder_day_diff, ['number' => $reminder_day_diff]) }}
                          ({{ \App\Helpers\DateHelper::getShortDate($reminder->getNextExpectedDate()) }})
                        </span>
                        <a href="/people/{{ $reminder->contact_id }}">{{ App\Contact::find($reminder->contact_id)->getCompleteName(auth()->user()->name_order) }}</a>:
                        {{ $reminder->getTitle() }}
                      </li>
                    @endforeach
                  </ul>

                  @else

                  <p>{{ trans('dashboard.reminders_blank_description') }}</p>

                  @endif
                </div>

                {{-- TASKS --}}
                <div class="tasks dashboard-section">
                  <img src="/img/people/tasks.svg" class="section-icon">
                  <h3>{{ trans('dashboard.tasks_title') }}</h3>

                  @if ($tasks->count() != 0)
                    @foreach ($tasks as $task)
                    <div class="dashboard-item">
                      <div class="truncate">
                        <a href="/people/{{ $task->contact_id }}">{{ App\Contact::find($task->contact_id)->getCompleteName(auth()->user()->name_order) }}</a>:
                        {{ $task->getTitle() }}
                        {{ $task->getDescription() }}
                      </div>
                    </div>
                    @endforeach
                  @else
                  <p>{{ trans('dashboard.tasks_blank') }}</p>
                  @endif
                </div>

                {{-- DEBTS --}}
                <div class="debts dashboard-section">
                  <img src="/img/people/debt/bill.svg" class="section-icon">
                  <h3>{{ trans('dashboard.section_debts') }}</h3>

                  @if ($debts->count() != 0)
                    @foreach ($debts as $debt)
                    <div class="dashboard-item">
                      <div class="truncate">

                        <a href="/people/{{ $debt->contact_id }}">{{ App\Contact::find($debt->contact_id)->getCompleteName(auth()->user()->name_order) }}</a>:

                        @if ($debt->in_debt == 'yes')
                        <span class="debt-description">{{ trans('dashboard.debts_you_owe') }}</span>
                        @else
                        <span class="debt-description">{{ trans('dashboard.debts_you_due') }}</span>
                        @endif

                        {{ MoneyHelper::format($debt->amount) }}

                        @if (! is_null($debt->reason))
                        <span class="debt-description">{{ trans('dashboard.debts_for') }}</span>
                        {{ $debt->reason }}
                        @endif
                      </div>
                    </div>
                    @endforeach
                  @else

                  <p>{{ trans('dashboard.debts_blank') }}</p>

                  @endif
                </div>
              </div>
              <div class="tab-pane" id="actions" role="tabpanel">
                <h3>{{ trans('dashboard.event_title') }}</h3>
                <ul class="event-list">
                  @foreach($events as $event)
                    <li class="event-list-item">

                      {{-- ICON--}}
                      <div class="event-icon">
                        @if ($event['nature_of_operation'] == 'create')
                          <i class="fa fa-plus-square-o"></i>
                        @endif

                        @if ($event['nature_of_operation'] == 'update')
                          <i class="fa fa-pencil-square-o"></i>
                        @endif
                      </div>

                      {{-- DESCRIPTION --}}
                      <div class="event-description">

                        {{-- YOU ADDED/YOU UPDATED --}}
                        @if ($event['nature_of_operation'] == 'create')
                          {{ trans('dashboard.event_create') }}
                        @endif

                        @if ($event['nature_of_operation'] == 'update')
                          {{ trans('dashboard.event_update') }}
                        @endif

                        {{-- PEOPLE --}}
                        @if ($event['object_type'] == 'contact')
                          @include('dashboard.events._people')
                        @endif

                        {{-- REMINDERS --}}
                        @if ($event['object_type'] == 'reminder')
                          @include('dashboard.events._reminders')
                        @endif

                        {{-- SIGNIFICANT OTHER --}}
                        @if ($event['object_type'] == 'significantother')
                          @include('dashboard.events._significantothers')
                        @endif

                        {{-- KIDS --}}
                        @if ($event['object_type'] == 'kid')
                          @include('dashboard.events._kids')
                        @endif

                        {{-- NOTES --}}
                        @if ($event['object_type'] == 'note')
                          @include('dashboard.events._notes')
                        @endif

                        {{-- ACTIVITIES --}}
                        @if ($event['object_type'] == 'activity')
                          @include('dashboard.events._activities')
                        @endif

                        {{-- TASKS --}}
                        @if ($event['object_type'] == 'task')
                          @include('dashboard.events._tasks')
                        @endif

                        {{-- GIFTS --}}
                        @if ($event['object_type'] == 'gift')
                          @include('dashboard.events._gifts')
                        @endif

                        {{-- DEBTS --}}
                        @if ($event['object_type'] == 'debt')
                          @include('dashboard.events._debts')
                        @endif

                      </div>

                      {{-- DATE --}}
                      <div class="event-date pull-right">
                        {{ $event['date']->diffForHumans() }}
                      </div>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>

          </div>

          {{-- Sidebar --}}
          <div class="col-xs-12 col-md-3 sidebar">

            <!-- Add activity  -->
            <div class="sidebar-cta hidden-xs-down">
              <a href="/people/add" class="btn btn-primary">{{ trans('app.main_nav_cta') }}</a>
            </div>

            <div class="sidebar-box last-seen">
              <h3>{{ trans('dashboard.tab_last_edited_contacts') }}</h3>
              <ul>
                @foreach ($lastUpdatedContacts as $contact)
                  <li><a href="/people/{{ $contact->id }}">{{ $contact->getCompleteName(auth()->user()->name_order) }}</a></li>
                @endforeach
              </ul>
            </div>

          </div>
        </div>
      </div>

    </div>

  </div>
@endsection
