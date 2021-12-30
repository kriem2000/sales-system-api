<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Type;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Type::create([
            'name'=>'Liquid',
            'desc' => "يتم دمج الجزء النشط من الدواء مع سائل لتسهيل تناوله أو امتصاصه بشكل أفضل. قد يسمى السائل أيضًا 'خليط' أو 'محلول' أو 'شراب'. تتوفر الآن العديد من السوائل الشائعة دون أي تلوين أو سكر مضاف.",
        ]);

        Type::create([
            'name'=>'Tablet',
            'desc' => "يتم دمج العنصر النشط مع مادة أخرى ويتم ضغطه في مادة صلبة مستديرة أو بيضاوية. هناك أنواع مختلفة من الأقراص. يمكن إذابة الأقراص القابلة للذوبان أو القابلة للتشتت بأمان في الماء."
        ]);

        Type::create([
            'name'=>'Capsules',
            'desc' => "الجزء النشط من الدواء موجود داخل غلاف بلاستيكي يذوب ببطء في المعدة. يمكنك تفكيك بعض الكبسولات ومزج المحتويات مع طعام طفلك المفضل. يحتاج البعض الآخر إلى البلع كاملاً ، لذلك لا يتم امتصاص الدواء حتى يتفكك حمض المعدة في غلاف الكبسولة."
        ]);

        Type::create([
            'name'=> 'Topical medicines',
            'desc' => "هذه هي الكريمات أو المستحضرات أو المراهم التي توضع مباشرة على الجلد. تأتي في أحواض أو زجاجات أو أنابيب حسب نوع الدواء. يتم خلط الجزء الفعال من الدواء بمادة أخرى ، مما يسهل دهنه على الجلد."
        ]);

        Type::create([
            'name'=> 'Suppositories',
            'desc' => "يتم دمج الجزء الفعال من الدواء مع مادة أخرى ويتم ضغطه في 'شكل رصاصة' بحيث يمكن إدخاله في الجزء السفلي. يجب عدم ابتلاع التحاميل."
        ]);

        Type::create([
            'name'=> 'Drops',
            'desc' => "غالبًا ما تستخدم حيث يعمل الجزء النشط من الدواء بشكل أفضل إذا وصل إلى المنطقة المصابة مباشرة. تميل إلى استخدامها للعين أو الأذن أو الأنف."
        ]);

        Type::create([
            'name'=> 'Inhalers',
            'desc' => "يتم إطلاق الجزء النشط من الدواء تحت الضغط مباشرة في الرئتين. قد يحتاج الأطفال الصغار إلى استخدام جهاز 'مباعد' لأخذ الدواء بشكل صحيح. قد يكون من الصعب استخدام أجهزة الاستنشاق في البداية ، لذا سيوضح لك الصيدلي كيفية استخدامها."
        ]);
        Type::create([
            'name'=> 'Injections',
            'desc' => "هناك أنواع مختلفة من الحقن ، كيف وأين يتم حقنها. يتم إعطاء الحقن تحت الجلد أو الحقن تحت سطح الجلد مباشرة تحت سطح الجلد. يتم إعطاء الحقن العضلي أو الحقن العضلي في العضل. يتم حقن الحقن داخل القراب في السائل المحيط بالحبل الشوكي. يتم إعطاء الحقن في الوريد أو الحقن الوريدي في الوريد. يمكن إعطاء بعض الحقن في المنزل ولكن يتم إعطاء معظمها في عيادة طبيبك أو في المستشفى."
        ]);
        Type::create([
            'name'=> 'Implants or patches ',
            'desc' => "These medicines are absorbed through the skin, such as nicotine patches for help in giving up smoking, or contraceptive implants. "
        ]);
        Type::create([
            'name'=> 'buccal or sublingual tablets',
            'desc' => "These look like normal tablets or liquids, but you don’t swallow them. Buccal medicines are held in the cheek so the mouth lining absorbs the active ingredient. Sublingual medicines work in the same way but are put underneath the tongue. Buccal and sublingual medicines tend only to be given in very specific circumstances. "
        ]);

    }
}
