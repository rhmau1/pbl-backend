<?php

namespace App\Services;

use App\Repositories\AnalyticsRepository;

final class AnalyticsService
{
    public function __construct(
        private ?AnalyticsRepository $repo = null,
    ) {
        $this->repo ??= new AnalyticsRepository();
    }

    public function countDraftProjects(): int
    {
        return $this->repo->countDraftProjects();
    }

    public function countReviewProjects(): int
    {
        return $this->repo->countReviewProjects();
    }

    public function countPublishedProjects(): int
    {
        return $this->repo->countPublishedProjects();
    }

    public function countDraftNews(): int
    {
        return $this->repo->countDraftNews();
    }

    public function countReviewNews(): int
    {
        return $this->repo->countReviewNews();
    }

    public function countPublishedNews(): int
    {
        return $this->repo->countPublishedNews();
    }

    public function getTotalLikes(): int
    {
        return $this->repo->getTotalLikes();
    }

    public function getTotalViews(): int
    {
        return $this->repo->getTotalViews();
    }

    public function getRecentProjectActivities(int $limit = 10): array
    {
        return $this->repo->getRecentProjectActivities($limit);
    }

    public function getRecentNewsActivities(int $limit = 10): array
    {
        return $this->repo->getRecentNewsActivities($limit);
    }

    public function getProjectPublicationPercentage(): float
    {
        $published = $this->repo->countPublishedProjects();
        $total = $this->repo->countTotalProjects();

        if ($total === 0) {
            return 0.0;
        }

        return round(($published / $total) * 100, 2);
    }

    public function getProjectAndNewsCountsByMonth(): array
    {
        return $this->repo->getProjectAndNewsCountsByMonth();
    }
}
