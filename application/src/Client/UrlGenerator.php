<?php declare(strict_types=1);


namespace App\Client;

use App\Entity\Building;
use App\Entity\Field;
use App\Entity\Player;
use App\Entity\Product;

class UrlGenerator
{
    public function getFarmUrl(Player $player)
    {
        return $this->getMainPart($player) . '&mode=getfarms&farm=1&position=0';
    }

    public function getGardenInitUrl(Building $building, Player $player)
    {
        return $this->getMainPart($player) . '&mode=gardeninit&farm=' . $building->getFarm()->getFarmIndex() . '&position=' . $building->getPosition();
    }

    public function getGardenHarvestUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_harvest&farm=' . $farmland->farm->id . '&position=' . $farmland->position . '&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->index . '&felder[]=' . $field->getRelatedFields();
    }

    public function getCropGardenUrl(Building $farmland)
    {
        return $this->getMainPart($farmland->getPlayer()) . '&mode=cropgarden&farm=' . $farmland->getFarm()->getFarmIndex() . '&position=' . $farmland->getPosition();
    }

    public function getGardenPlantUrl(Building $farmland, Field $field, Product $product)
    {
        return $this->getMainPart($farmland->getPlayer()) . '&mode=garden_plant&farm=' . $farmland->getFarm()->getFarmIndex() . '&position=' . $farmland->getPosition()
//            . $singleBunchOfFields->getUrlPartWithProduct($product);// '&pflanze[]=' . $product->getPid() . '&feld[]=' . $singleBunchOfFields->getIndexes() . '&felder[]=' . $singleBunchOfFields->getRelatedFields();
            //&pflanze[]=17&feld[]=36&felder[]=36
            . '&pflanze[]=' . $product->getPid()
            . '&feld[]=' . $field->getPosition()
            . '&felder[]=' . $field->getPosition();
    }

    public function getGardenWaterUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_water&farm=' . $farmland->farm->id . '&position=' . $farmland->position . '&feld[]=' . $field->index . '&felder[]=' . $field->getRelatedFields();
    }

    public function getFeedUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_feed&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition() . '&pid=1&c=1_1|&amount=1&guildjob=0';
    }

    public function getLoadHovelDataUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_init&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition();
    }

    public function getCollectEggsUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_crop&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition();
    }

    public function getFeedChickensUrl(Hovel $hovel, Product $plant)
    {
//        http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c933f7ed67fd832e960743ad4b&mode=inner_feed&farm=1&position=2&pid=2&c=2_1|&amount=1&guildjob=0
//        http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c933f7ed67fd832e960743ad4b&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0
        return $this->getMainPart() . '&mode=inner_feed&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition() . '&pid=' . $plant->getPid() . '&c=1_1|&amount=1&guildjob=0';
    }

    private function getMainPart(Player $player)
    {
        return 'http://s' . $player->getServerId() . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $player->getToken();
    }
}