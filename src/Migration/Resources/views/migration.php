<?php echo '<?php' ?>

namespace Application\Migrations;

use Arlekin\Dbal\Migration\MigrationInterface;

class <?php echo $className ?> implements MigrationInterface
{
    public function up()
    {
        <?php echo $upFunctionContent ?>
    }

    public function getVersion()
    {
        return <?php echo $version ?>;
    }
}