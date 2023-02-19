<?php
namespace App\Common;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

trait CacheUsers {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function UserListCached() {
        $cache = new FilesystemAdapter();
        // The callable will only be executed on a cache miss.
        $cache->get('users_list', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $allUsers = $this->doctrine->getRepository(User::class)->findAll(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $allUsers;
        });
    }

}

trait CacheListUserName {

    public function UserNameList($name) {
        $cache = new FilesystemAdapter();
        $latestUsers = $cache->getItem('users_list');
        if (!$latestUsers->isHit()) {
            $usersData = $this->cached->UserListCached();
            $cache->save($latestUsers->set($usersData));
        } else {
            $usersData = $latestUsers->get();
        }

        $listUserName = [];
        foreach ($usersData as $key => $value) {
            $listUserName[] = $value->getName();
        }
        return array_search($name,$listUserName);
    }

}

trait CacheListUserEmail {

    public function UserEmailList($email) {
        $cache = new FilesystemAdapter();
        $latestUsers = $cache->getItem('users_list');
        if (!$latestUsers->isHit()) {
            $usersData = $this->cached->UserListCached();
            $cache->save($latestUsers->set($usersData));
        } else {
            $usersData = $latestUsers->get();
        }

        $listUserEmail = [];
        foreach ($usersData as $key => $value) {
            $listUserEmail[] = $value->getEmail();
        }
        return array_search($email,$listUserEmail);
    }

}

trait CacheListUsers {

    public function UserList() {
        $cache = new FilesystemAdapter();
        $latestUsers = $cache->getItem('users_list');
        if (!$latestUsers->isHit()) {
            $usersData = $this->cached->UserListCached();
            $cache->save($latestUsers->set($usersData));
        } else {
            $usersData = $latestUsers->get();
        }
        return $usersData;
    }

}
class Cache {
    use CacheUsers,CacheListUserName,CacheListUserEmail,CacheListUsers;
}
