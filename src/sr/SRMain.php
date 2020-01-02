<?php
/**
 * Created by PhpStorm.
 * User: Wayde Finley
 * Date: 12/31/2019
 * Time: 9:30 PM
 */
namespace sr;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SRMain extends PluginBase implements Listener {
    private $c;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->notice("Enabled");
        @mkdir($this->getDataFolder());

        $this->c = new Config($this->getDataFolder().'SaveRod.yml',Config::YAML,[
            'ItemName' => "§r§k§c||§r §l§6SaveRod§r §k§c||§r",
            "Lore" => ["§7- Saves your inventory!"],
            "Saved Msg" => "Your SaveRod saved your inventory!"
        ]);
    }

    public function saveRod(EntityDamageByEntityEvent $ev) {
        $ent = $ev->getEntity();

        $i = Item::get(ItemIds::BLAZE_ROD);

        if ($ent instanceof Player){
            if ($ev->getFinalDamage() >= $ent->getHealth() && $ent->getInventory()->contains($i)){
                $ent->setHealth($ent->getMaxHealth());
                $ent->setFood($ent->getMaxFood());

                $ent->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                $ent->sendMessage($this->getC()->getAll()['Saved Msg']);
                $ent->getInventory()->removeItem($i);
                $ev->setCancelled();
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender->hasPermission('sr.give')) return false;

        $all = $this->getC()->getAll();

        if ($command->getName() === 'sr'){
            if (isset($args[0])){
                $p = $this->getServer()->getPlayer($args[0]);
                $i = Item::get(ItemIds::BLAZE_ROD);

                $i->setLore($all['Lore']);

                $i->setCustomName($all['ItemName']);

                $p->getInventory()->addItem($i);
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getC() {
        return $this->c;
    }

    public function onDisable() {
        $this->getLogger()->notice("Disabled");
    }
}