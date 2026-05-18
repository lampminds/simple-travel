<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatHelperTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_helper_translations')->delete();
        
        \DB::table('cat_helper_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'helper_id' => 1,
                'language_id' => 2,
                'text' => '<p>Ejemplos de servicio gastronómico:</p><ul><li><p>Pizza napolitana</p></li><li><p>Plato de fideos</p></li><li><p>Menú turístico</p></li><li><p>Hamburguesa completa</p></li><li><p>Desayuno buffet</p></li><li><p>Cena show</p></li></ul><p>Para ver ejemplos de variantes, vea la ayuda en las variantes.</p>',
            ),
            1 => 
            array (
                'id' => 2,
                'helper_id' => 2,
                'language_id' => 2,
                'text' => '<p>Servicio: Pizza mozzarella</p><p>Variantes:</p><ul><li><p>chica</p></li><li><p>mediana</p></li><li><p>grande</p></li></ul><p>Servicio: Menú turístico</p><p>Variantes:</p><ul><li><p>solo menú</p></li><li><p>menú + bebida</p></li><li><p>menú + postre</p></li><li><p>menú completo</p></li></ul>',
            ),
        ));
        
        
    }
}