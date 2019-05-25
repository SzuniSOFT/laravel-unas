<?php


namespace SzuniSoft\Unas\Tests\Client;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use SzuniSoft\Unas\Model\Product;

class GetProductTest extends AuthenticatedTestCase
{

    use WithFaker;

    /** @test */
    public function can_return_with_products()
    {
        $this->createSnapshotClient()
            ->getProducts()
            ->chunk(function (Collection $products) {
                $this->assertTrue($products->isNotEmpty());
            });
    }


    /** @test */
    public function can_return_single_product()
    {
        $this->assertInstanceOf(
            Product::class,
            $product = $this->createSnapshotClient()
                ->getProduct($this->faker->uuid)
        );

        $this->assertSame('live', $product->state);
        $this->assertSame('200000', $product->id);
        $this->assertSame('Cikkszam2', $product->sku);
        $this->assertSame(false, $product->explicit);
        $this->assertSame('Termék név 2', $product->name);
        $this->assertSame('csomag', $product->unit);
        $this->assertEquals(1, $product->minimum_qty);
        $this->assertEquals(5, $product->maximum_qty);
        $this->assertEquals(5, $product->alert_qty);
        $this->assertSame('2', $product->unit_step);
        $this->assertEquals(10, $product->alter_unit->qty);
        $this->assertSame('db', $product->alter_unit->unit);
        $this->assertEquals(1, $product->weight);
        $this->assertEquals(10, $product->point);
        $this->assertSame('rövid leírás', $product->short_description);
        $this->assertSame('ez már hosszabb', $product->long_description);
        $this->assertEquals(27, $product->vat);
        $this->assertSame('http://teszt.unas.hu/Cikkszam1', $product->url);

        /** @var \SzuniSoft\Unas\Model\ProductPrice $firstPrice */
        /** @var \SzuniSoft\Unas\Model\ProductPrice $secondPrice */
        /** @var \SzuniSoft\Unas\Model\ProductPrice $thirdPrice */
        /** @var \SzuniSoft\Unas\Model\ProductPrice $fourthPrice */
        /** @var \SzuniSoft\Unas\Model\ProductPrice $fifthPrice */
        /** @var \SzuniSoft\Unas\Model\ProductPrice $sixthPrice */

        $firstPrice = $product->prices->get(0);
        $this->assertSame('normal', $firstPrice->type);
        $this->assertEquals(2000, $firstPrice->net);
        $this->assertEquals(2540, $firstPrice->gross);

        $secondPrice = $product->prices->get(1);
        $this->assertSame('sale', $secondPrice->type);
        $this->assertEquals('2015.01.01', $secondPrice->start->format('Y.m.d'));
        $this->assertNull($secondPrice->end);
        $this->assertEquals(1000, $secondPrice->net);
        $this->assertEquals(1270, $secondPrice->gross);

        $thirdPrice = $product->prices->get(2);
        $this->assertSame('special', $thirdPrice->type);
        $this->assertEquals(100, $thirdPrice->group);
        $this->assertEquals(393.7008, $thirdPrice->net);
        $this->assertEquals(500, $thirdPrice->gross);

        $fourthPrice = $product->prices->get(3);
        $this->assertSame('special', $fourthPrice->type);
        $this->assertEquals(200, $fourthPrice->area);
        $this->assertSame('Belföld', $fourthPrice->area_name);
        $this->assertEquals(100, $fourthPrice->group);
        $this->assertSame('Törzsvásárló', $fourthPrice->group_name);
        $this->assertEquals(629.9212, $fourthPrice->net);
        $this->assertEquals(800, $fourthPrice->gross);

        $fifthPrice = $product->prices->get(4);
        $this->assertSame('special', $fifthPrice->type);
        $this->assertEquals(200, $fifthPrice->area);
        $this->assertEquals(200, $fifthPrice->group);
        $this->assertEquals(1000, $fifthPrice->net);
        $this->assertEquals(1270, $fifthPrice->gross);
        $this->assertEquals(629.9212, $fifthPrice->sale_net);
        $this->assertEquals(800, $fifthPrice->sale_gross);
        $this->assertSame('2017.01.01', $fifthPrice->sale_start->format('Y.m.d'));
        $this->assertSame('2017.12.31', $fifthPrice->sale_end->format('Y.m.d'));

        $sixthPrice = $product->prices->get(5);
        $this->assertSame('special', $sixthPrice->type);
        $this->assertEquals(300, $sixthPrice->area);
        $this->assertEquals(300, $sixthPrice->group);
        $this->assertEquals(20, $sixthPrice->percent);

        /** @var \SzuniSoft\Unas\Model\ProductImage $firstImage */
        /** @var \SzuniSoft\Unas\Model\ProductImage $secondImage */

        $firstImage = $product->images->get(0);
        $this->assertSame('base', $firstImage->type);
        $this->assertSame('http://teszt.unas.hu/…/Cikkszam1.jpg', $firstImage->small_url);
        $this->assertSame('http://teszt.unas.hu/…/Cikkszam1.jpg', $firstImage->medium_url);
        $this->assertSame('http://teszt.unas.hu/…/Cikkszam1.jpg', $firstImage->big_url);

        $secondImage = $product->images->get(1);
        $this->assertSame('alt', $secondImage->type);
        $this->assertSame('http://teszt.unas.hu/…/Cikkszam1_altpic_1.jpg', $secondImage->small_url);
        $this->assertSame('http://teszt.unas.hu/…/Cikkszam1_altpic_1.jpg', $secondImage->medium_url);
        $this->assertNull($secondImage->big_url);


        /** @var \SzuniSoft\Unas\Model\ProductVariant $firstVariant */
        /** @var \SzuniSoft\Unas\Model\ProductVariant $secondVariant */

        $firstVariant = $product->variants->get(0);
        $this->assertSame('Szín', $firstVariant->name);

        $this->assertSame('Kék', $firstVariant->values->get(0)->name);
        $this->assertSame('Piros', $firstVariant->values->get(1)->name);
        $this->assertEquals(100, $firstVariant->values->get(1)->extra_price);

        $secondVariant = $product->variants->get(1);
        $this->assertSame('Méret', $secondVariant->name);

        $this->assertSame('S', $secondVariant->values->get(0)->name);
        $this->assertEquals(-100, $secondVariant->values->get(0)->extra_price);

        $this->assertSame('M', $secondVariant->values->get(1)->name);

        $this->assertSame('L', $secondVariant->values->get(2)->name);
        $this->assertEquals(200, $secondVariant->values->get(2)->extra_price);


        $this->assertEquals(1, $product->datas->get(0)->id);
        $this->assertSame('Garancia', $product->datas->get(0)->name);
        $this->assertSame('Egy év', $product->datas->get(0)->value);

        $this->assertEquals(2, $product->datas->get(1)->id);
        $this->assertSame('Szállítási Határidő', $product->datas->get(1)->name);
        $this->assertSame('Egy hét', $product->datas->get(1)->value);


        $this->assertEquals(1001, $product->params->get(0)->id);
        $this->assertSame('text', $product->params->get(0)->type);
        $this->assertSame('Paraméter 1', $product->params->get(0)->name);
        $this->assertSame('Csoport 1', $product->params->get(0)->group);
        $this->assertSame('Környakú', $product->params->get(0)->value);

        $this->assertEquals(1002, $product->params->get(1)->id);
        $this->assertSame('textmore', $product->params->get(1)->type);
        $this->assertSame('Paraméter 2', $product->params->get(1)->name);
        $this->assertSame('Érték 1, Érték 2, Érték 3', $product->params->get(1)->value);

        $this->assertEquals(1003, $product->params->get(2)->id);
        $this->assertSame('enum', $product->params->get(2)->type);
        $this->assertSame('Paraméter 3', $product->params->get(2)->name);
        $this->assertSame('Érték 1, Érték 2, Érték 3', $product->params->get(2)->value);

        $this->assertEquals(1004, $product->params->get(3)->id);
        $this->assertSame('enummore', $product->params->get(3)->type);
        $this->assertSame('Paraméter 4', $product->params->get(3)->name);
        $this->assertSame('Érték 1, Érték 2, Érték 3', $product->params->get(3)->value);

        $this->assertEquals(1005, $product->params->get(4)->id);
        $this->assertSame('num', $product->params->get(4)->type);
        $this->assertSame('Paraméter 5', $product->params->get(4)->name);
        $this->assertSame('100', $product->params->get(4)->value);
        $this->assertSame('akármi', $product->params->get(4)->before);
        $this->assertSame('db', $product->params->get(4)->after);

        $this->assertEquals(1006, $product->params->get(5)->id);
        $this->assertSame('interval', $product->params->get(5)->type);
        $this->assertSame('Paraméter 6', $product->params->get(5)->name);
        $this->assertSame('100 - 200', $product->params->get(5)->value);
        $this->assertSame('akármi', $product->params->get(5)->before);
        $this->assertSame('db', $product->params->get(5)->after);

        $this->assertEquals(1007, $product->params->get(6)->id);
        $this->assertSame('color', $product->params->get(6)->type);
        $this->assertSame('Paraméter 7', $product->params->get(6)->name);
        $this->assertSame('#ff00ff', $product->params->get(6)->value);

        $this->assertEquals(1008, $product->params->get(7)->id);
        $this->assertSame('link', $product->params->get(7)->type);
        $this->assertSame('Paraméter 8', $product->params->get(7)->name);
        $this->assertSame('http://unas.hu', $product->params->get(7)->value);

        $this->assertEquals(1009, $product->params->get(8)->id);
        $this->assertSame('linkblank', $product->params->get(8)->type);
        $this->assertSame('Paraméter 9', $product->params->get(8)->name);
        $this->assertSame('http://shop.unas.hu', $product->params->get(8)->value);

        $this->assertEquals(1010, $product->params->get(9)->id);
        $this->assertSame('link_text', $product->params->get(9)->type);
        $this->assertSame('Paraméter 10', $product->params->get(9)->name);
        $this->assertSame('http://shop.unas.hu - UnasShop', $product->params->get(9)->value);

        $this->assertEquals(1011, $product->params->get(10)->id);
        $this->assertSame('html', $product->params->get(10)->type);
        $this->assertSame('Paraméter 11', $product->params->get(10)->name);
        $this->assertSame('Teszt <b>szöveg</b>', $product->params->get(10)->value);

        $this->assertEquals(1012, $product->params->get(11)->id);
        $this->assertSame('icon', $product->params->get(11)->type);
        $this->assertSame('Paraméter 12', $product->params->get(11)->name);
        $this->assertSame('1', $product->params->get(11)->value);

        $this->assertEquals(1013, $product->params->get(12)->id);
        $this->assertSame('iconmore', $product->params->get(12)->type);
        $this->assertSame('Paraméter 13', $product->params->get(12)->name);
        $this->assertSame('1, 3, 10', $product->params->get(12)->value);

        $this->assertEquals(1014, $product->params->get(13)->id);
        $this->assertSame('pic', $product->params->get(13)->type);
        $this->assertSame('Paraméter 14', $product->params->get(13)->name);
        $this->assertSame('pic_194908_api_teszt.jpg', $product->params->get(13)->value);

        $this->assertEquals(1015, $product->params->get(14)->id);
        $this->assertSame('piclink', $product->params->get(14)->type);
        $this->assertSame('Paraméter 15', $product->params->get(14)->name);
        $this->assertSame('akarmi.jpg', $product->params->get(14)->value);

        $this->assertEquals(1016, $product->params->get(15)->id);
        $this->assertSame('piclinktext', $product->params->get(15)->type);
        $this->assertSame('Paraméter 16', $product->params->get(15)->name);
        $this->assertSame('akarmi.jpg - AKÁRMI', $product->params->get(15)->value);

        $this->assertEquals(1017, $product->params->get(16)->id);
        $this->assertSame('date', $product->params->get(16)->type);
        $this->assertSame('Paraméter 17', $product->params->get(16)->name);
        $this->assertSame('2019.01.01', $product->params->get(16)->value);


        $this->assertEquals(0, $product->qty_discounts->get(0)->limit_lower);
        $this->assertEquals(10, $product->qty_discounts->get(0)->limit_upper);
        $this->assertEquals(0, $product->qty_discounts->get(0)->discount);

        $this->assertEquals(10, $product->qty_discounts->get(1)->limit_lower);
        $this->assertEquals(20, $product->qty_discounts->get(1)->limit_upper);
        $this->assertEquals(5, $product->qty_discounts->get(1)->discount);

        $this->assertEquals(20, $product->qty_discounts->get(2)->limit_lower);
        $this->assertNull($product->qty_discounts->get(2)->limit_upper);
        $this->assertEquals(10, $product->qty_discounts->get(2)->discount);


        $this->assertSame('70828162', $product->additional_products->get(0)->id);
        $this->assertSame('Cikkszam1', $product->additional_products->get(0)->sku);
        $this->assertSame('Termék név 1', $product->additional_products->get(0)->name);

        $this->assertSame('63226347', $product->additional_products->get(1)->id);
        $this->assertSame('Cikkszam3', $product->additional_products->get(1)->sku);
        $this->assertSame('Termék név 3', $product->additional_products->get(1)->name);

        $this->assertSame('85478568', $product->similar_products->get(0)->id);
        $this->assertSame('Cikkszam5', $product->similar_products->get(0)->sku);
        $this->assertSame('Termék név 5', $product->similar_products->get(0)->name);


    }

    /** @test */
    public function return_null_when_product_not_found()
    {
        $this->assertNull(
            $this->createSnapshotClient()
                ->getProduct($this->faker->uuid)
        );
    }

}
