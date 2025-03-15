<?php
//FORAI:F2620;DEF[C2227:TestEntity<BaseEntity>,C2228:Account<TestEntity>,C2229:Team<TestEntity>,C2230:EntityTeam<TestEntity>,C2231:Contact<TestEntity>,C2232:Post<TestEntity>,C2233:Comment<TestEntity>,C2234:PostData<TestEntity>,C2235:Tag<TestEntity>,C2236:PostTag<TestEntity>,C2237:Note<TestEntity>,C2238:Article<TestEntity>,C2239:Job<TestEntity>,C2240:Test<TestEntity>,C2241:Dependee<TestEntity>,C2242:TestWhere<TestEntity>,C2243:TestSelect<TestEntity>,C2244:TestSelectRight<TestEntity>,F10681:getFromContainerOriginal];IMP[];EXP[C2227,C2228,C2229,C2230,C2231,C2232,C2233,C2234,C2235,C2236,C2237,C2238,C2239,C2240,C2241,C2242,C2243,C2244,F10681];LANG[php]//


namespace tests\unit\testData\DB;

use Espo\ORM\BaseEntity;

class TestEntity extends BaseEntity
{
    public ?string $id = null;
}

class Account extends TestEntity
{


}

class Team extends TestEntity
{

}

class EntityTeam extends TestEntity
{

}

class Contact extends TestEntity
{

}

class Post extends TestEntity
{

}

class Comment extends TestEntity
{

}

class PostData extends TestEntity
{

}

class Tag extends TestEntity
{

}

class PostTag extends TestEntity
{

}

class Note extends TestEntity
{

}


class Article extends TestEntity
{

}

class Job extends TestEntity
{
    public function getFromContainerOriginal(string $attribute)
    {
        return $this->getFromContainer($attribute);
    }
}

class Test extends TestEntity
{

}

class Dependee extends TestEntity
{

}

class TestWhere extends TestEntity
{

}

class TestSelect extends TestEntity
{
}

class TestSelectRight extends TestEntity
{
}
