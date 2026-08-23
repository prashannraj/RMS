<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Caste;
use App\Models\Category;
use App\Models\District;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\LocalBody;
use App\Models\MasterConfiguration;
use App\Models\MotherTongue;
use App\Models\Organization;
use App\Models\Post;
use App\Models\Qualification;
use App\Models\Quota;
use App\Models\Religion;
use App\Models\Service;
use App\Models\State;
use App\Models\SubGroup;

class MasterDataService
{
    public function get(string $type, array $filters = [])
    {
        return match ($type) {
            'states' => $this->list(State::query(), $filters),
            'districts' => $this->list(District::query()->with('state'), $filters),
            'local-bodies' => $this->list(LocalBody::query()->with(['district', 'state']), $filters),
            'castes' => $this->list(Caste::query(), $filters),
            'religions' => $this->list(Religion::query(), $filters),
            'mother-tongues' => $this->list(MotherTongue::query(), $filters),
            'qualifications' => $this->list(Qualification::query(), $filters),
            'quotas' => $this->list(Quota::query(), $filters),
            'posts' => $this->list(Post::query(), $filters),
            'groups' => $this->list(Group::query(), $filters),
            'sub-groups' => $this->list(SubGroup::query(), $filters),
            'faculties' => $this->list(Faculty::query(), $filters),
            'boards' => $this->list(Board::query(), $filters),
            'services' => $this->list(Service::query(), $filters),
            'organizations' => $this->list(Organization::query(), $filters),
            'categories' => $this->list(Category::query(), $filters),
            default => throw new \InvalidArgumentException("Unknown master data type: {$type}"),
        };
    }

    public function getConfigurations(array $filters = [], int $perPage = 50)
    {
        $query = MasterConfiguration::query();

        if (!empty($filters['post_id'])) {
            $query->where('post_id', $filters['post_id']);
        }

        if (!empty($filters['key'])) {
            $query->where('config_key', $filters['key']);
        }

        return $query->paginate($perPage);
    }

    private function list($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                foreach (['name', 'name_np', 'name_en'] as $column) {
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Master data lists are typically small; cap at 500 rows
        return $query->limit(500)->get();
    }
}