<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Activitylog\Models\Activity;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', number_format(User::query()->count()))
                ->description('Registered accounts')
                ->color('primary'),
            Stat::make('Total Posts', number_format(Post::query()->count()))
                ->description('Published discussion posts')
                ->color('primary'),
            Stat::make('Total Comments', number_format(Comment::query()->count()))
                ->description('Active comments and replies')
                ->color('primary'),
            Stat::make('Deactivated Accounts', number_format(User::onlyTrashed()->count()))
                ->description('Soft-deleted user accounts')
                ->color('primary'),
            Stat::make('Activity Logs', number_format(Activity::query()->count()))
                ->description('System actions')
                ->color('primary'),
        ];
    }
}
