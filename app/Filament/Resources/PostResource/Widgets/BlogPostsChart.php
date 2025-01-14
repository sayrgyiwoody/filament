<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Carbon\Carbon;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class BlogPostsChart extends ChartWidget
{

    use InteractsWithPageFilters;

    protected static ?string $heading = 'Post Chart';

    protected function getData(): array
    {

        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;


        $data = Trend::model(Post::class)
            ->between(
                start: $startDate ? Carbon::parse($startDate)->subMonths(6) : now()->subMonths(6),
                end: $endDate ? Carbon::parse($endDate) : now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
