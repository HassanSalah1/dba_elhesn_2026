@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
@endsection

@section('page-style')
    <style>
        /* ================================================================
           Dba Elhesn Dashboard — Color Identity from dh_logo.png
           Primary Green:  #1B7340  (shield border, palm trees)
           Dark Brown:     #6B3427  (tower, palm trunks)
           Navy Blue:      #3B5998  (ocean waves)
           Light Green:    #e8f5ed  (background tint)
           Light Blue:     #eaf1f8  (background tint)
           ================================================================ */
        :root {
            --dh-green: #1B7340;
            --dh-green-light: #22905a;
            --dh-green-bg: rgba(27, 115, 64, 0.08);
            --dh-brown: #6B3427;
            --dh-brown-bg: rgba(107, 52, 39, 0.08);
            --dh-navy: #3B5998;
            --dh-navy-bg: rgba(59, 89, 152, 0.08);
            --dh-light-green: #e8f5ed;
            --dh-light-blue: #eaf1f8;
        }

        /* Welcome Banner */
        .card-welcome-banner {
            background: linear-gradient(135deg, #1B7340 0%, #22905a 50%, #3B5998 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(27, 115, 64, 0.2) !important;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: none;
            min-height: 140px;
        }
        .card-welcome-banner::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -8%;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            pointer-events: none;
        }
        .card-welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: 10%;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            pointer-events: none;
        }
        .card-welcome-banner .welcome-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
        }

        /* Section Divider */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .section-divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, var(--dh-green), transparent);
            border-radius: 2px;
        }
        .section-divider h3 {
            color: var(--dh-green) !important;
            white-space: nowrap;
        }

        /* Stats Card Base */
        .card-stat {
            border-radius: 16px;
            border: 1px solid rgba(27, 115, 64, 0.06);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03) !important;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        .card-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        .card-stat:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(27, 115, 64, 0.12) !important;
            border-color: rgba(27, 115, 64, 0.15);
        }
        .card-stat:hover::before {
            opacity: 1;
        }

        /* Card Color Variants */
        .card-stat.variant-green::before { background: linear-gradient(90deg, #1B7340, #22905a); }
        .card-stat.variant-brown::before { background: linear-gradient(90deg, #6B3427, #8b4a3a); }
        .card-stat.variant-navy::before  { background: linear-gradient(90deg, #3B5998, #5b7bb5); }

        /* Icon Container */
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.35s ease;
        }
        .stat-icon svg {
            width: 24px !important;
            height: 24px !important;
        }
        .card-stat:hover .stat-icon {
            transform: scale(1.08) rotate(-3deg);
        }

        /* Icon Color Variants */
        .stat-icon.icon-green {
            background: var(--dh-green-bg);
            color: var(--dh-green);
        }
        .stat-icon.icon-brown {
            background: var(--dh-brown-bg);
            color: var(--dh-brown);
        }
        .stat-icon.icon-navy {
            background: var(--dh-navy-bg);
            color: var(--dh-navy);
        }

        /* Stat value styling */
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #718096;
            margin: 0;
        }

        /* Quick action link on card */
        .card-stat a {
            text-decoration: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        {{-- Welcome Banner --}}
        <div class="col-12 mb-2">
            <div class="card card-welcome-banner p-2 mb-0">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex flex-column justify-content-center">
                        <h2 class="text-white fw-bolder mb-75" style="font-size: 1.5rem;">
                            مرحباً بك مجدداً،
                            <span style="font-family: 'Montserrat', sans-serif; direction: ltr; display: inline-block;">{{ auth()->user()->name ?? 'مدير النظام' }}</span>
                            👋
                        </h2>
                        <p class="text-white mb-0" style="max-width: 620px; font-size: 1.05rem; line-height: 1.7; opacity: 0.85;">
                            أنت الآن في لوحة تحكم تطبيق نادي دبا الحصن الرياضي الثقافي. يمكنك تتبع آخر إحصائيات النشاط والمحتوى وإدارة جميع عناصر النظام من هنا.
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <img src="{{ asset('images/logo/dh_logo.png') }}" alt="شعار نادي دبا الحصن" class="welcome-logo">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Title --}}
        <div class="col-12 mb-1 mt-50">
            <div class="section-divider">
                <h3 class="mb-0 fw-bolder" style="font-size: 1.2rem;">{{ trans('admin.statistics') }}</h3>
            </div>
        </div>

        {{-- Row 1: Users, News, Events, Sport Games --}}
        {{-- Users --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/users') }}">
                <div class="card card-stat variant-green h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $usersCount }}</div>
                            <p class="stat-label">{{ trans('admin.usersCount') }}</p>
                        </div>
                        <div class="stat-icon icon-green">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- News --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/news') }}">
                <div class="card card-stat variant-brown h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $newsCount }}</div>
                            <p class="stat-label">{{ trans('admin.news_title') }}</p>
                        </div>
                        <div class="stat-icon icon-brown">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Events --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/actions') }}">
                <div class="card card-stat variant-navy h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $actionsCount }}</div>
                            <p class="stat-label">{{ trans('admin.actions_title') }}</p>
                        </div>
                        <div class="stat-icon icon-navy">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Sport Games --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/sportGames') }}">
                <div class="card card-stat variant-green h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $sportGamesCount }}</div>
                            <p class="stat-label">{{ trans('admin.sport_games_title') }}</p>
                        </div>
                        <div class="stat-icon icon-green">
                            <i data-feather="award"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Row 2: Subscriptions, Contacts, Clinic, Gallery --}}
        {{-- Subscriptions --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/sport/subscription') }}">
                <div class="card card-stat variant-navy h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $subscribesCount }}</div>
                            <p class="stat-label">{{ trans('admin.subscription_title') }}</p>
                        </div>
                        <div class="stat-icon icon-navy">
                            <i data-feather="credit-card"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Contact Messages --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/contacts') }}">
                <div class="card card-stat variant-brown h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $contactsCount }}</div>
                            <p class="stat-label">{{ trans('admin.contacts_title') }}</p>
                        </div>
                        <div class="stat-icon icon-brown">
                            <i data-feather="message-circle"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Clinic Bookings --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/clinic/bookings') }}">
                <div class="card card-stat variant-green h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $clinicBookingsCount }}</div>
                            <p class="stat-label">{{ trans('admin.clinic_bookings_title') }}</p>
                        </div>
                        <div class="stat-icon icon-green">
                            <i data-feather="activity"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gallery --}}
        <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
            <a href="{{ url('/admin/galleries') }}">
                <div class="card card-stat variant-navy h-100 bg-white mb-0">
                    <div class="card-body d-flex align-items-center justify-content-between py-1">
                        <div>
                            <div class="stat-value">{{ $galleriesCount }}</div>
                            <p class="stat-label">{{ trans('admin.gallery_title') }}</p>
                        </div>
                        <div class="stat-icon icon-navy">
                            <i data-feather="image"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection
