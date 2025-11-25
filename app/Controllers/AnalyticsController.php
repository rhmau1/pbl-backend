<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\ResponseFormatter;
use App\Services\AnalyticsService;

final class AnalyticsController extends Controller
{
    public function __construct(private ?AnalyticsService $svc = null)
    {
        $this->svc ??= new AnalyticsService();
    }

    public function countDraftProjects(Request $req, Response $res): Response
    {
        $count = $this->svc->countDraftProjects();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function countReviewProjects(Request $req, Response $res): Response
    {
        $count = $this->svc->countReviewProjects();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function countPublishedProjects(Request $req, Response $res): Response
    {
        $count = $this->svc->countPublishedProjects();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function countDraftNews(Request $req, Response $res): Response
    {
        $count = $this->svc->countDraftNews();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function countReviewNews(Request $req, Response $res): Response
    {
        $count = $this->svc->countReviewNews();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function countPublishedNews(Request $req, Response $res): Response
    {
        $count = $this->svc->countPublishedNews();

        return $res->json(
            ResponseFormatter::success('Success', $count, 200),
            200
        );
    }

    public function getTotalLikes(Request $req, Response $res): Response
    {
        $likes = $this->svc->getTotalLikes();

        return $res->json(
            ResponseFormatter::success('Success', $likes, 200),
            200
        );
    }

    public function getTotalViews(Request $req, Response $res): Response
    {
        $views = $this->svc->getTotalViews();

        return $res->json(
            ResponseFormatter::success('Success', $views, 200),
            200
        );
    }

    public function getRecentProjectActivities(Request $req, Response $res): Response
    {
        $limit = (int) ($req->query['limit'] ?? 10);
        $activities = $this->svc->getRecentProjectActivities($limit);

        return $res->json(
            ResponseFormatter::success('Success', $activities, 200),
            200
        );
    }

    public function getRecentNewsActivities(Request $req, Response $res): Response
    {
        $limit = (int) ($req->query['limit'] ?? 10);
        $activities = $this->svc->getRecentNewsActivities($limit);

        return $res->json(
            ResponseFormatter::success('Success', $activities, 200),
            200
        );
    }
}
