<?php

namespace App\Http\Transformers;

use App\Models\Degree;
use App\Models\Residence;
use App\Models\User;
use Carbon\Carbon;

class UserTransformer extends BaseTransformer
{
    protected $fields = ['id', 'self', 'contact', 'promo', 'gadz', 'photos', 'addresses', 'residences', 'courses',
        'tags', 'degrees', 'responsibilities', 'jobs', 'socials'];

    protected $fields_minimal = ['id', 'self', 'contact', 'promo', 'gadz'];

    public function transform(User $user)
    {
        $data = [
            'id'               => $user->id,
            'self'             => app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('users.show', $user->id),
            'contact'          => [
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'birthday'  => $user->birthday->format('Y-m-d'),
                'gender'    => $user->gender,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'photo'     => $user->profile_photo,
            ],
            'promo'            => [
                'campus' => $this->itemArray($user->campus, new CampusTransformer),
                'year'   => (int)$user->year,
            ],
            'gadz'             => null,
            'photos'           => null,
            'addresses'        => null,
            'residences'       => null,
            'courses'          => null,
            'tags'             => $user->tags,
            'degrees'          => null,
            'responsibilities' => null,
            'jobs'             => null,
            'socials'          => null,
            'search'           => null, // special field for search result
        ];

        if ($gadz = $user->gadz) {
            $data['gadz'] = $this->itemArray($gadz, new GadzTransformer);
            $data['gadz']['promsTBK'] = $user->campus->prefix . $gadz->proms;
        }

        if ($photos = $user->photos and $photos->count()) {
            $data['photos'] = $this->collectionArray($photos, new PhotoTransformer);
        }

        if ($addresses = $user->addresses and $addresses->count()) {
            $data['addresses'] = $this->collectionArray($addresses, new AddressTransformer);
        }

        if ($residences = $user->residences and $residences->count()) {
            $data['residences'] = $this->collectionArray($residences, new ResidenceTransformer);
            $residence = $residences->sortByDesc('created_at')
                ->first(function ($uselessKeyFixedInLaravel53, Residence $residence) {
                    return $residence->pivot->from <= Carbon::now() && Carbon::now() <= $residence->pivot->to;
                }, null);

            if ($residence) {
                $data['search']['residence'] = $this->itemArray($residence, new ResidenceTransformer);
            }
        }

        if ($courses = $user->courses and $courses->count()) {
            $data['courses'] = $this->collectionArray($courses, new CourseTransformer);
        }

        if ($degrees = $user->degrees and $degrees->count()) {
            $data['degrees'] = $this->collectionArray($degrees, new DegreeTransformer);
            $degree = $degrees->sortByDesc('created_at')
                ->first(function ($uselessKeyFixedInLaravel53, Degree $degree) {
                    return $degree->am;
                }, null);

            if ($degree) {
                $data['search']['degree'] = $this->itemArray($degree, new DegreeTransformer);
            }
        }

        if ($responsabilities = $user->responsabilities and $responsabilities->count()) {
            $data['responsibilities'] = $this->collectionArray($responsabilities, new ResponsibilityTransformer);
        }

        if ($jobs = $user->jobs and $jobs->count()) {
            $data['jobs'] = $this->collectionArray($jobs, new JobTransformer);
        }

        if ($socials = $user->socials and $socials->count()) {
            $data['socials'] = $this->collectionArray($socials, new SocialTransformer);
        }

        $data = $this->filter($data);

        return $data;
    }
}
