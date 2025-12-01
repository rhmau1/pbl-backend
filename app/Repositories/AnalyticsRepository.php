<?php

namespace App\Repositories;

use Config\Database;
use PDO;

final class AnalyticsRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function countDraftProjects(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE status = 'draft'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countReviewProjects(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE status = 'review'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countPublishedProjects(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE status = 'published'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countDraftNews(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM news WHERE status = 'draft'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countReviewNews(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM news WHERE status = 'review'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function countPublishedNews(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM news WHERE status = 'published'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function getTotalLikes(): int
    {
        $st = $this->db->query("SELECT SUM(likes) FROM projects");
        return (int) $st->fetchColumn();
    }

    public function getTotalViews(): int
    {
        $st = $this->db->query("SELECT SUM(views) FROM projects");
        return (int) $st->fetchColumn();
    }

    public function getRecentProjectActivities(int $limit = 5): array
    {
        $st = $this->db->prepare("SELECT id, title, status, created_at, updated_at FROM projects ORDER BY created_at DESC LIMIT :limit");
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentNewsActivities(int $limit = 5): array
    {
        $st = $this->db->prepare("SELECT id, title, status, created_at, updated_at FROM news ORDER BY created_at DESC LIMIT :limit");
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTotalProjects(): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM projects");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public function getProjectAndNewsCountsByMonth(): array
    {
        $sql = "SELECT * FROM project_news_count_by_month_views";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
