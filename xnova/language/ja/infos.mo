<?php

// ----------------------------------------------------------------------------------------------------------
// Interface !
$lang['nfo_page_title']  = "Information";
$lang['nfo_title_head']  = "Information about";
$lang['nfo_name']        = "Name";
$lang['nfo_destroy']     = "Deconstruct";
$lang['nfo_level']       = "Level";
$lang['nfo_range']       = "Sensor range";
$lang['nfo_used_energy'] = "Energy Consumption";
$lang['nfo_used_deuter'] = "Deuterium Consumption";
$lang['nfo_prod_energy'] = "Energy Production	";
$lang['nfo_difference']  = "Difference";
$lang['nfo_prod_p_hour'] = "production/hour";
$lang['nfo_needed']      = "Requirements";
$lang['nfo_dest_durati'] = "Deconstruction Time";

$lang['nfo_struct_pt']   = "Structural Integrity";
$lang['nfo_shielf_pt']   = "Shield Strength";
$lang['nfo_attack_pt']   = "Attack Strength";
$lang['nfo_rf_again']    = "Rapidfire against";
$lang['nfo_rf_from']     = "Rapidfire from";
$lang['nfo_capacity']    = "Cargo Capacity";
$lang['nfo_units']       = "units";
$lang['nfo_base_speed']  = "Base speed";
$lang['nfo_consumption'] = "Fuel usage (Deuterium)";

// ----------------------------------------------------------------------------------------------------------
// Interface of Jump Gate
$lang['gate_start_moon'] = "Start moon";
$lang['gate_dest_moon']  = "Target moon :";
$lang['gate_use_gate']   = "Use jump gate";
$lang['gate_ship_sel']   = "Select spaceships";
$lang['gate_ship_dispo'] = "available";
$lang['gate_jump_btn']   = "Execute jump!";
$lang['gate_jump_done']  = "The Jump Gate is in recharge mode!<br>The Gate will be fully recharged for the next jump in: ";
$lang['gate_wait_dest']  = "Destination Jump Gate is in recharge mode!The Gate will be fully recharged for the next jump in: ";
$lang['gate_no_dest_g']  = "There is no jump gate on the planet where you want to send the fleet";
$lang['gate_wait_star']  = "The Jump Gate is in recharge mode!<br>The Gate will be fully recharged for the next jump in: ";
$lang['gate_wait_data']  = "Error, no jumping data!";

// ----------------------------------------------------------------------------------------------------------
// Buildings Mines!
$lang['info'][1]['name']          = "メタル採掘所";
$lang['info'][1]['description']   = "メタルは戦艦、建造物の建設に欠かせない重要な資源です。メタルは全ての資源の中で最も豊富なものですが、取引する上での価値は全ての資源の中で最も価値が低いです。採掘所がより深層部に到達するに応じて生産性は向上しますが、採掘にあたりより多くのエネルギー必要とします。";
$lang['info'][2]['name']          = "クリスタル採掘所";
$lang['info'][2]['description']   = "クリスタルは電子回路、一定の合金化合物の形成に使用される主な資源です。メタル製造工程に比べ、生粋のクリスタルから産業用クリスタルへの変換にはおよそ2倍のエネルギーが必要です。したがって取引する際クリスタルはより高価なものとなります。各機体、建造物にはある一定の量のクリスタルを必要としますが、適したクリスタルは希少で深層部で発見されます。より深層部のクリスタル採掘は高額となりますが、地表に近い部分に比べより多くのクリスタルの採掘が可能となります。";
$lang['info'][3]['name']          = "デューテリウム シンセサイザー";
$lang['info'][3]['description']   = "デューテリウムは重水素とも呼ばれ、深海で採掘されます。デューテリウム　シンセサイザーのアップグレード毎に、より多くのデューテリウムの採掘が可能となります。デューテリウムはセンサー軍団によるスキャン、銀河の表示、戦艦の燃料、リサーチレベルのアップグレードに使用されます。";

// ----------------------------------------------------------------------------------------------------------
// Buildings Energy!
$lang['info'][4]['name']          = "ソーラー プラント";
$lang['info'][4]['description']   = "巨大なソーラー プラントは採掘所、デューテリウム シンセサイザーのためのエネルギーを合成します。ソーラープラントのアップグレードにより、惑星の表面の太陽電池パネルの面積が上昇します。これにより多くのエネルギー合成が可能となります。ソーラープラントは惑星エネルギー供給の基本値を示します。";
$lang['info'][12]['name']         = "核融合炉";
$lang['info'][12]['description']  = "核融合炉では水素原子を膨大な圧力、高温によりヘリウム原子へ融合します。ヘリウム核は融合の結果、41.32*10^-13ジュールのエネルギーを放出します。(1グラムのデュートリウムから172 MWのエネルギーを生み出すことができます。)<br>核融合炉が大きいほど、更なるエネルギーを生成できます。<br>核融合炉のエネルギー生産の計算は次のようになっています:<br><br> 30 * [核融合炉のレベル] * (1,05 + [エネルギー技術のレベル] * 0,01) ^ [核融合炉のレベル]";

// ----------------------------------------------------------------------------------------------------------
// Buildings General!
$lang['info'][14]['name']         = "ロボティクス 工場";
$lang['info'][14]['description']  = "ロボティック工場は建造物の建設、建造物のアップグレードをするロボットを提供します。ロボティック工場の各アップグレードにより建設に使用されるロボティックユニット数、能率を向上し、建設時間の削減が可能となります。";
$lang['info'][15]['name']         = "ナノマシン工場";
$lang['info'][15]['description']  = "この工場では、ロボット工学技術における究極の発展型ロボットユニットを製造します。ナノマシンはネットワークを経由し並はずれた性能を発揮するナノメートルサイズのロボットです。一度リサーチされると、ナノマシンたちはあらゆる領域における生産性を向上します。ナノマシン工場により、各レベルにおける建造物、戦艦、防衛設備の建設時間が半分になります。";
$lang['info'][21]['name']         = "造船所";
$lang['info'][21]['description']  = "惑星造船所はスペースシップ、防衛システムの建設を行います。造船所のアップグレードにより様々な戦艦の製造をより早い期間で行うことができます。ナノマシン工場が存在している場合は、ユニット製造速度が格段に向上します。";
$lang['info'][22]['name']         = "メタル貯蔵庫";
$lang['info'][22]['description']  = "この建造物は採掘されたメタル鉱石のための巨大な貯蔵庫です。この建造物のアップグレードに伴い、より多くのメタル鉱石を貯蔵できます。メタル貯蔵庫の最大容量に達した時点で、それ以上のメタルを採掘することはできません。";
$lang['info'][23]['name']         = "クリスタル貯蔵庫";
$lang['info'][23]['description']  = "処理されていないクリスタルがこの建造物に格納されます。この建造物のアップグレードに伴い、クリスタルの貯蔵容量が向上します。クリスタル貯蔵庫の最大容量に達した時点で、それ以上のクリスタルを採掘することはできません。";
$lang['info'][24]['name']         = "デューテリウム タンク";
$lang['info'][24]['description']  = "この建造物は新しく抽出されたデューテリウムの貯蔵用に建設された巨大タンクです。デューテリウムは艦隊の建造に使用されるため、通常このデューテリウムタンクは造船所の付近に存在します。デューテリウムタンクのアップグレードに伴い、より多くのデューテリウムを貯蔵することができます。タンクの最大貯蔵容量に達した時点で、それ以上のデューテリウムを合成できません。";
$lang['info'][31]['name']         = "リサーチセンター";
$lang['info'][31]['description']  = "新しい領域の技術について研究するには、このリサーチセンターが必要です。リサーチセンターのアップグレードにより、新しい技術のリサーチ速度が向上するだけでなく、新しい領域の技術におけるリサーチの幅を広げることができます。可能な限り短い時間でリサーチを行うために、全てのリサーチ情報はリサーチ課題が開始された惑星に転送されます。リサーチ課題の完了次第、科学者たちは新しく研究された技術をホーム惑星に持ち帰ります。このように、容易に帝国中に新しい技術に関する知識を広めることができます。";
$lang['info'][33]['name']         = "テラフォーマー";
$lang['info'][33]['description']  = "惑星の開発の増大により、制限された居住面積は深刻な問題となります。高層建造物、地下工事などの伝統的な方法ではこの問題に対する十分な改善策にはなりません。高エネルギー物理学者、ナノ技術技術者たちはついに改善策を見つけました：それがテラフォーミング<br>莫大なエネルギー消費により、テラフォーマーは帯状の陸地だけでなく、大陸全体をも開墾することができます。この建造物では、特別に改良されたナノマシンの製造により、一定の品質の土壌を提供します。<br> 一度テラフォーマーが建設されると、取り壊すことができません。";
$lang['info'][34]['name']         = "同盟補給基地";
$lang['info'][34]['description']  = "同盟補給基地は軌道上の防御支援を行っている友好艦隊に燃料を供給します。同盟補給基地のそれぞれのアップグレードレベルにおいて、10,000ユニットのデューテリウム/時を軌道上の艦隊に送ることができます。";

// ----------------------------------------------------------------------------------------------------------
// Batiments Lune!
$lang['info'][41]['name']         = "Lunar Base";
$lang['info'][41]['description']  = "Since a moon has no atmosphere and is an extremely hostile environment, a lunar base must first be built before the moon can be developed. The Lunar Base provides oxygen, heating, and gravity to create a living environment for the colonists. With each level constructed, a larger living and development area is provided within the biosphere. With each level of the Lunar Base constructed, three fields are developed for other buildings.<br><br> Once built, the lunar base can not be torn down.";
$lang['info'][42]['name']         = "Sensor Phalanx";
$lang['info'][42]['description']  = "Utilizing high-resolution sensors, the Sensor Phalanx first scans the spectrum of light, composition of gases, and radiation emissions from a distant world and transmits the data to a supercomputer for processing. Once the information is obtained, the supercomputer compares changes in the spectrum, gas composition, and radiation emissions, to a base line chart of known changes of the spectrum created by various ship movements. The resulting data then displays activity of any fleet within the range of the phalanx. To prevent the supercomputer from overheating during the process, it is cooled by utilizing 5k of processed Deuterium.<br><br> To use the Phalanx, click on any planet in the Galaxy View within your sensors range.";
$lang['info'][43]['name']         = "Jump Gate";
$lang['info'][43]['description']  = "A Jump Gate is a system of giant transceivers capable of sending even the largest fleets to a receiving Gate anywhere in the universe without loss of time. Utilizing technology similar to that of a Worm Hole to achieve the jump, deuterium is not required. A recharge period of one hour must pass between jumps to allow for regeneration. Transporting resources through the Gate is not possible.";

$lang['info'][44]['name']         = "ミサイル塔";
$lang['info'][44]['description']  = "ミサイル塔はミサイルの組立て、格納、発射に使用します。各開発レベルに応じて5発の星間ミサイル、または10発の抗弾道ミサイルが格納可能です。1発の星間ミサイルは2発分の抗弾道ミサイルと同じスペースが必要です。スペースに収まる範囲内であれば、異なるミサイルタイプを一度に格納することも可能です。";

// ----------------------------------------------------------------------------------------------------------
// Laboratory !
$lang['info'][106]['name']        = "Espionage Technology";
$lang['info'][106]['description'] = "Espionage Technology is your intelligence gathering tool. This technology allows you to view your targets resources, fleets, buildings, and research levels using specially designed probes. Launched on your target, these probes transmit back to your planet an encrypted data file that is fed into a computer for processing. After processing, the information on your target is then displayed for evaluation.<br> With Espionage Technology, the level of your technology to that of your target is critical. If your target has a higher level of Espionage Technology than you, you will need to launch more probes to gather all the information on your target. However this runs the great risk of detection by your target, resulting in the probes destruction. However, launching too few probes will result in missing information that is most critical, which could result in the total destruction of your fleet if an attack is launched.<br><br> At certain levels of Espionage Technology research, new attack warning systems are installed:<br><br> At Level <font color=red> 2</font>, the total number of attacking ships will be displayed along with the simple attack warning.<br><br> At Level <font color=red>4</font>, the type of attacking ships along with the number of ships are displayed.<br><br> At Level <font color=red>8</font>, the exact number of each type of ship launched is displayed.";
$lang['info'][108]['name']        = "Computer Technology";
$lang['info'][108]['description'] = "Once launched on any mission, fleets are controlled primarily by a series of computers located on the originating planet. These massive computers calculate the exact time of arrival, controls course corrections as needed, calculates trajectories, and regulates flight speeds.<br><br>With each level researched, the flight computer is upgraded to allow an additional slot to be launched. Computer technology should be continuously developed throughout the building of your empire.";
$lang['info'][109]['name']        = "Weapons Technology";
$lang['info'][109]['description'] = "Weapons Technology is a key research technology and is critical to your survival against enemy Empires. With each level of Weapons Technology researched, the weapons systems on ships and your defense mechanisms become increasingly more efficient. Each level increases the base strength of your weapons by 10% of the base value.";
$lang['info'][110]['name']        = "Shielding Technology";
$lang['info'][110]['description'] = "With the invention of the magnetosphere generator, scientists learned that an artificial shield could be produced to protect the crew in space ships not only from the harsh solar radiation environment in deep space, but also provide protection from enemy fire during an attack. Once scientists finally perfected the technology, a magnetosphere generator was installed on all ships and defence systems.<br><br>As the technology is advanced to each level, the magnetosphere generator is upgraded which provides an additional 10% strength to the shields base value.";
$lang['info'][111]['name']        = "Armour Technology";
$lang['info'][111]['description'] = "The environment of deep space is harsh. Pilots and crew on various missions not only faced intense solar radiation, they also faced the prospect of being hit by space debris, or destroyed by enemy fire in an attack. With the discovery of an aluminum-lithium titanium carbide alloy, which was found to be both light weight and durable, this afforded the crew a certain degree of protection. With each level of Armour Technology developed, a higher quality alloy is produced, which increases the armours strength by 10%.";
$lang['info'][113]['name']        = "Energy Technology";
$lang['info'][113]['description'] = "As various researches were advancing, it was discovered that the current technology of energy distribution was not sufficient enough to begin certain specialized researches. With each upgrade of your Energy Technology, new researches can be conducted which unlocks development of more sophisticated ships and defenses.";
$lang['info'][114]['name']        = "Hyperspace Technology";
$lang['info'][114]['description'] = "In theory, the idea of hyperspace travel relies on the existence of a separate and adjacent dimension. When activated, a hyperspace drive shunts the starship into this other dimension, where it can cover vast distances in an amount of time greatly reduced from the time it would take in \"normal\" space. Once it reaches the point in hyperspace that corresponds to its destination in real space, it re-emerges.<br>Once a sufficient level of Hyperspace Technology is researched, the Hyperspace Drive is no longer just a theory.";
$lang['info'][115]['name']        = "Combustion Drive";
$lang['info'][115]['description'] = "The Combustion Drive is the oldest of technologies, but is still in use. With the Combustion Drive, exhaust is formed from propellants carried within the ship prior to use. In a closed chamber, the pressures are equal in each direction and no acceleration occurs. If an opening is provided at the bottom of the chamber then the pressure is no longer opposed on that side. The remaining pressure gives a resultant thrust in the side opposite the opening, which propels the ship forward by expelling the exhaust rearwards at extreme high speed.<br><br>With each level of the Combustion Drive developed, the speed of small and large cargo ships, light fighters, recyclers, and espionage probes are increased by 10%.";
$lang['info'][117]['name']        = "Impulse Drive";
$lang['info'][117]['description'] = "The impulse drive is essentially an augmented fusion rocket, usually consisting of a fusion reactor,an accelerator-generator, a driver coil assembly and a vectored thrust nozzle to direct the plasma exhaust. The fusion reaction generates a highly energized plasma. This plasma, (\"electro-plasma\") can be employed for propulsion, or can be diverted through the EPS to the power transfer grid, via EPS conduits, so as to supply other systems. The accelerated plasma is passed through the driver coils, thereby generating a subspace field which improves the propulsive effect.<br><br>With each level of the Impulse Drive developed, the speed of bombers, cruisers, heavy fighters, and colony ships are increased by 20% of the base value. Interplanetary missiles also travel farther with each level.";
$lang['info'][118]['name']        = "Hyperspace Drive";
$lang['info'][118]['description'] = "With the advancement of Hyperspace Technology, the Hyperspace Drive was created. Hyperspace is an alternate region of space co-existing with our own universe which may be entered using an energy field or other device. The HyperSpace Drive utilizes this alternate region by distorting the space-time continuum, which results in speeds that exceed the speed of light (otherwise known as FTL travel). During FTL travel, time and space is warped to the point that results in a trip that would normally take 1000 light years to be completed, to be accomplished in about an hour.<br><br>With each level the Hyperspace Drive is developed, the speed of battleships, battlecruisers, destroyers, and deathstars are increased by 30%.";
$lang['info'][120]['name']        = "Laser Technology";
$lang['info'][120]['description'] = "In physics, a laser is a device that emits light through a specific mechanism for which the term laser is an acronym: Light Amplification by Stimulated Emission of Radiation. Lasers have many uses to the empire, from upgrading computer communications systems to the creation of newer weapons and space ships.";
$lang['info'][121]['name']        = "Ion Technology";
$lang['info'][121]['description'] = "Simply put, an ion is an atom or a group of atoms that has acquired a net electric charge by gaining or losing one or more electrons. Utilized in advanced weapons systems, a consentrated beam of Ions can cause considerable damage to objects that it strikes.";
$lang['info'][122]['name']        = "Plasma Technology";
$lang['info'][122]['description'] = "In the universe, there exists four states of matter: solid, liquids, gas, and plasma. Being an advanced version of Ion technology, Plasma Technology expands on the destructive effect that Ion Technology delivered, and opens the door to create advanced weapons systems and ships. Plasma matter is created by superheating gas and compressing it with extreme high pressures to create a sphere of superheated plasma matter. The resulting plasma sphere causes considerable damage to the target in which the sphere is launched to.";
$lang['info'][123]['name']        = "Intergalactic Research Network";
$lang['info'][123]['description'] = "This is your deep space network to communicate researches to your colonies. With the IRN, faster research times can be achieved by linking the highest level research labs equal to the level of the IRN developed.<br><br>In order to function, each colony must be able to conduct the research independently.";
$lang['info'][124]['name']        = "Expedition Technology";
$lang['info'][124]['description'] = "The Expedition Technology includes several scan researches and allows you to equip different spaceships with research modules to explore uncharted regions of the universe. Those include a database and a fully functional mobile laboratory.<br><br>To assure the security of the expedition fleet during dangerous research situations, the research modules have their own energy supplies and energy field generators which creates a powerful force field around the research module during emergency situations.";
$lang['info'][199]['name']        = "Graviton Technology";
$lang['info'][199]['description'] = "The graviton is an elementary particle that mediates the force of gravity in the framework of quantum field theory. The graviton must be massless (because the gravitational force has unlimited range) and must have a spin of 2 (because gravity is a second-rank tensor field). Graviton Technology is only used for one thing, for the construction of the fearsome DeathStar.<br><br>Out of all of the technologies to research, this one carries the most risk of detection during the phase of preparation.";

// ----------------------------------------------------------------------------------------------------------
// Fleet !
$lang['info'][202]['name']        = "Small Cargo";
$lang['info'][202]['description'] = "The first ship built by any emperor, the small cargo is an agile resource moving ship that has a cargo capacity of 5,000 resource units. This multi-use ship not only has the ability to quickly transport resources between your colonies, but also accompanies larger fleets on raiding missions on enemy targets. [Ship refitted with Impulse Drives once reached level 5]";
$lang['info'][203]['name']        = "Large Cargo";
$lang['info'][203]['description'] = "As time evolved, the raids on colonies resulted in larger and larger amounts of resources being captured. As a result, Small Cargos were being sent out in mass numbers to compensate for the larger captures. It was quickly learned that a new class of ship was needed to maximize resources captured in raids, yet also be cost effective. After much development, the Large Cargo was born.<br><br>To maximize the resources that can be stored in the holds, this ship has little in the way of weapons or armor. Thanks to the highly developed combustion engine installed, it serves as the most economical resource supplier between planets, and most effective in raids on hostile worlds.";
$lang['info'][204]['name']        = "Light Fighter";
$lang['info'][204]['description'] = "This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable by themselves. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargo to hostile planets with minor defenses.";
$lang['info'][205]['name']        = "Heavy Fighet";
$lang['info'][205]['description'] = "In developing the heavy fighter, researchers reached a point at which conventional drives no longer provided sufficient performance. In order to move the ship optimally, the impulse drive was used for the first time. This increased the costs, but also opened new possibilities. By using this drive, there was more energy left for weapons and shields; in addition, high-quality materials were used for this new family of fighters. With these changes, the heavy fighter represents a new era in ship technology and is the basis for cruiser technology.<br><br>Slightly larger than the light fighter, the heavy fighter has thicker hulls, providing more protection, and stronger weaponry.";
$lang['info'][206]['name']        = "Cruiser";
$lang['info'][206]['description'] = "With the development of the heavy laser and the ion cannon, light and heavy fighters encountered an alarmingly high number of defeats that increased with each raid. Despite many modifications, weapons strength and armour changes, it could not be increased fast enough to effectively counter these new defensive measures. Therefore, it was decided to build a new class of ship that combined more armor and more firepower. As a result of years of research and development, the Cruiser was born.<br><br>Cruisers are armored almost three times of that of the heavy fighters, and possess more than twice the firepower of any combat ship in existence. They also possess speeds that far surpassed any spacecraft ever made. For almost a century, cruisers dominated the universe. However, with the development of Gauss cannons and plasma turrets, their predominance ended. They are still used today against fighter groups, but not as predominantly as before.";
$lang['info'][207]['name']        = "Battleship";
$lang['info'][207]['description'] = "Once it became apparent that the cruiser was losing ground to the increasing number of defense structures it was facing, and with the loss of ships on missions at unacceptable levels, it was decided to build a ship that could face those same type of defense structures with as little loss as possible. After extensive development, the Battleship was born. Built to withstand the largest of battles, the Battleship features large cargo spaces, heavy cannons, and high hyperdrive speed. Once developed, it eventually turned out to be the backbone of every raiding Emperors fleet.";
$lang['info'][208]['name']        = "Colony Ship";
$lang['info'][208]['description'] = "In the 20th Century, Man decided to go for the stars. First, it was landing on the Moon. After that, a space station was built. Mars was colonized soon afterwards. It was soon determined that our growth depended on colonizing other worlds. Scientists and engineers all over the world gathered together to develop mans greatest achievement ever. The Colony Ship is born.<br><br>This ship is used to prepare a newly discovered planet for colonization. Once it arrives at the destination, the ship is instantly transformed into habital living space to assist in populating and mining the new world. 9 Planets maximum can be colonized.";
$lang['info'][209]['name']        = "Recycler";
$lang['info'][209]['description'] = "As space battles became larger and more fierce, the resultant debris fields became too large to gather safely by conventional means. Normal transporters could not get close enough without receiving substantial damage. A solution was developed to this problem. The Recycler.<br><br>Thanks to the new shields and specially built equipment to gather wreckage, gathering debris no longer presented a danger. Each Recycler can gather 20,000 units of debris.";
$lang['info'][210]['name']        = "Espionage Probe";
$lang['info'][210]['description'] = "Espionage probes are small, agile drones that provide data on fleets and planets. Fitted with specially designed engines, it allows them to cover vast distances in only a few minutes. Once in orbit around the target planet, they quickly collect data and transmit the report back via your Deep Space Network for evaluation. But there is a risk to the intelligent gathering aspect. During the time the report is transmitted back to your network, the signal can be detected by the target and the probes can be destroyed.";
$lang['info'][211]['name']        = "Bomber";
$lang['info'][211]['description'] = "Over the centuries, as defenses were starting to get larger and more sophisticated, fleets were starting to be destroyed at an alarming rate. It was decided that a new ship was needed to break defenses to ensure maximum results. After years of research and development, the Bomber was created.<br><br>Using laser-guided targeting equipment and Plasma Bombs, the Bomber seeks out and destroys any defense mechanism it can find. As soon as the hyperspace drive is developed to Level 8, the Bomber is retrofitted with the hyperspace engine and can fly at higher speeds.";
$lang['info'][212]['name']        = "Solar Satellite";
$lang['info'][212]['description'] = "It quickly became apparent that more energy was needed to power larger mines then could be produced by conventional ground based solar planets and fusion reactors. Scientists worked on the problem and discovered a method of transmitting electrical energy to the colony using specially designed satellites in geosynchronous orbit.<br><br>Solar Satellites gather solar energy and transmit it to a ground station using advanced laser technology. The efficiency of a solar satellite depends on the strength of the solar radiation it receives. In principle, energy production in orbits closer to the sun is greater than for planets in orbits distant from the sun. Since the satellites primary goal is the transmission of energy, they lack shielding and weapons capability, and because of this they are usually destroyed in large numbers in a major battle. However they do possess a small self-defense mechanism to defend itself in an espionage mission from an enemy empire if the mission is detected.";
$lang['info'][213]['name']        = "Destroyer";
$lang['info'][213]['description'] = "The Destroyer is the result of years of work and development. With the development of Deathstars, it was decided that a class of ship was needed to defend against such a massive weapon.Thanks to its improved homing sensors, multi-phalanx Ion cannons, Gauss Cannons and Plasma Turrets, the Destroyer turned out to be one of the most fearsome ships created.<br><br>Because the destroyer is very large, its maneuverability is severely limited, which makes it more of a battle station than a fighting ship. The lack of maneuverability is made up for by its sheer firepower, but it also costs significant amounts of deuterium to build and operate.";
$lang['info'][214]['name']        = "Deathstar";
$lang['info'][214]['description'] = "The Deathstar is the ultimate ship ever created. This moon sized ship is the only ship that can be seen with the naked eye on the ground. By the time you spot it, unfortunately, it is too late to do anything.<br><br>Armed with a gigantic graviton cannon, the most advanced weapons system ever created in the Universe, this massive ship has not only the capability of destroying entire fleets and defenses, but also has the capability of destroying entire moons. Only the most advanced empires have the capability to build a ship of this mammoth size.";
$lang['info'][215]['name']        = "Battlecruiser";
$lang['info'][215]['description'] = "This ship is one of the most advanced fighting ships ever to be developed, and is particularly deadly when it comes to destroying attacking fleets. With its improved laser cannons on board and advanced Hyperspace engine, the Battlecruiser is a serious force to be dealt with in any attack.<br><br>Due to the ships design and its large weapons system, the cargo holds had to be cut, but this is compensated for by the lowered fuel consumption.";

// ----------------------------------------------------------------------------------------------------------
// Defense !
$lang['info'][401]['name']        = "Rocket Launcher";
$lang['info'][401]['description'] = "Your first basic line of defense. These are simple ground based launch facilities that fire conventional warhead tipped missiles at attacking enemy targets. As they are cheap to construct with and no research is required, they are well suited for defending raids, but lose effectiveness defending from larger scale attacks. Once you begin construction on more advanced defense weapons systems, Rocket Launchers become simple fodder to allow your more damaging weapons to inflict greater damage for a longer period of time.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][402]['name']        = "Light Laser";
$lang['info'][402]['description'] = "As technology developed and more sophisticated ships were created, it was determined that a stronger level of defense was needed to counter the attacks. As Laser Technology advanced, a new weapon was designed to provide the next level of defense. Light Lasers are simple ground based weapons that utilizes a special targeting systems to track your enemy and fire a high intensity laser designed to cut through the hull of the target. In order to be kept cost effective, they were fitted with an improved shielding system, however the structural integrity is the same of that as the Rocket Launcher.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][403]['name']        = "Heavy Laser";
$lang['info'][403]['description'] = "The Heavy Laser is a practical, improved version of the Light Laser. Being more balanced than the Light Laser with improved alloy composition, stronger, more densely packed beams, and even better onboard targeting systems.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][404]['name']        = "Gauss Cannon";
$lang['info'][404]['description'] = "Far from being a science-fiction \"weapon of tomorrow,\" the concept of a weapon using an electromagnetic impulse for propulsion originated as far back as the mid-to-late 1930s. Basically, the Gauss Cannon consists of a system of powerful electromagnets which fires a projectile by accelerating between a number of metal rails. Gauss Cannons fire high-density metal projectiles at extremely high velocity.<br><br>This weapon is so powerful when fired that it creates a sonic boom which is heard for miles, and the crew near the weapon must take special precautions due to the massive concussion effects generated.";
$lang['info'][405]['name']        = "Ion Cannon";
$lang['info'][405]['description'] = "An ion cannon is a weapon that fires beams of ions (particles, i.e. atoms that have been affected in some way as to cause them to gain an electrical charge). The Ion Cannon is actually a type of Particle Cannon; only the particles used are ionized. Due to their electrical charges, they also have the potential to disable electronic devices, and anything else that has an electrical or similar power source, using a phenomena known as the the Electromagetic Pulse (EMP effect). Due to the cannons highly improved shielding system, this cannon provides improved protection for your larger, more destructive defense weapons.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][406]['name']        = "Plasma Turret";
$lang['info'][406]['description'] = "One of the most advanced defense weapons systems ever developed, the Plasma Turret uses a large nuclear reactor fuel cell to power an electromagnetic accelerator that fires a pulse, or toroid, of plasma. During operation, the Plasma turret first locks in on a target and begins the process of firing. A plasma sphere is created in the turrets core by super heating and compressing gases, stripping them of their ions. Once the gas is superheated, compressed, and a plasma sphere is created, it is then loaded into the electromagnetic accelerator which is then energized. Once fully energized, the accelerator is then activated, which results in the plasma sphere being launched at an extremely high rate of speed to the intended target. From your targets perspective, the approaching bluish ball of plasma is impressive, but once it strikes, it causes instant destruction.<br><br>Defensive facilities deactivate as soon as they are too heavily damaged. After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][407]['name']        = "Small Shield Dome";
$lang['info'][407]['description'] = "Colonizing new worlds brought about a new danger, space debris. A large asteroid could easily wipe out the world and all inhabitants. Advancements in shielding technology provided scientists with a way to develop a shield to protect an entire planet not only from space debris but, as it was learned, from an enemy attack. By creating a large electromagnetic field around the planet, space debris that would normally have destroyed the planet was deflected, and attacks from enemy Empires were thwarted. The first generators were large and the shield provided moderate protection, but it was later discovered that small shields did not afford the protection from larger scale attacks. The small shield dome was the prelude to a stronger, more advanced planetary shielding system to come.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][408]['name']        = "Large Shield Dome";
$lang['info'][408]['description'] = "Large Shield Domes are the next step in the advancement of planetary shields, and is the result of years of work improving the Small Shield Dome. Built to withstand a larger barrage of enemy fire by providing a higher energized electromagnetic field, large domes provide a longer period of protection before collapsing.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";

// ----------------------------------------------------------------------------------------------------------
// Missiles !
$lang['info'][502]['name']        = "Anti-Ballistic Missiles";
$lang['info'][502]['description'] = "Anti Ballistic Missiles (ABM) are your only line of defense when attacked by Interplanetary Missiles (IPM). When a launch of IPMs is detected, these missiles automatically arm, process a launch code in their flight computers, target the inbound IPM, and launch to intercept. During the flight, the target IPM is constantly tracked and course corrections are applied until the ABM reaches the target and destroys the attacking IPM. Each ABM destroys one incoming IPM.<br><br>Each level of your missile silo developed can store 10 ABMs, 5 IPMs, or a combination of both missile types.";
$lang['info'][503]['name']        = "Interplanetary Missiles";
$lang['info'][503]['description'] = "Interplanetary Missiles (IPM) is your offensive weapon to destroy the defenses of your target. Using state of the art tracking technology, each missile targets a certain number of defenses for destruction. Tipped with an anti-matter bomb, they deliver a destructive force so severe that destroyed shields and defenses cannot be repaired. The only way to counter these missiles is with APMs.<br><br>Each level of your missile silo developed can store 10 ABMs, 5 IPMs, or a combination of both missile types.";

// ----------------------------------------------------------------------------------------------------------
// Officiers !
$lang['info'][601]['name']        = "Geologist";
$lang['info'][601]['description'] = "The Geologist is a expert in astro-mineralogy and crystalography. He assists his teams in metallurgy and chemistry as he also takes care of the interplanetary communications optimizing the use and refining of the raw material along the empire. Utilizing state of the art equipment for surveying, the Geologist can locate optimal areas for mining and increasing mining production. <br><br><font color=red>+5% mining production per level. Max level: 20</font>";
$lang['info'][602]['name']        = "Admiral";
$lang['info'][602]['description'] = "The admiral of the fleet is a war veteran and a strategist feared. Even when the fight is hard, it keeps the cold-blooded needed to dominate the situation and is in constant contact with the admirals under his command. An emperor should not be responsible for passing the admiral of the fleet to coordinate its attacks and can make such confidence that it can send more fleets in combat.<br><br><font color=red>+5% on shields per level. Max level. : 20</font>";
$lang['info'][603]['name']        = "Engineer";
$lang['info'][603]['description'] = "The engineer is a specialist in the management of energy. In peace time, it maximizes the efficiency of energy networks colonies.<br><br><font color=red>+5% emergy per level. Max levels: 10</font>";
$lang['info'][604]['name']        = "Technocrat";
$lang['info'][604]['description'] = "Guilds of technocrats are recognized scientific genius. They are found in areas where technology is reaching its limits. Nobody will succeed in deciphering the encryption of a technocrat, his mere presence inspires researchers across the empire.<br><br><font color=red>Increasing speed of ship construction by 5% per level. Max levels: 10</font>";
$lang['info'][605]['name']        = "Manufacturer";
$lang['info'][605]['description'] = "The manufacturer is a new kind of a builder. His DNA was modified to give it superhuman strength. Only one of these \"man\" can build an entire city.<br><br><font color=red>increasing 10% of construction speed per level. Max levels: 3</font>";
$lang['info'][606]['name']        = "Scientist";
$lang['info'][606]['description'] = "Scientists are part of a guild concurente than technocrats. They specialize in the improvement of technology.<br><br><font color=red>Increasing 10% of researching speed per level. Max levels: 3</font>";
$lang['info'][607]['name']        = "Storekeeper";
$lang['info'][607]['description'] = "The storekeeper is part of the former Brotherhood of the planet Hsac. His motto is to win a maximum, but for this reason it needs storage space as important thing. With the manufacturer he will developed a new storage technique.<br><br><font color=red> +50% of storage per level. Max levels: 2</font>";
$lang['info'][608]['name']        = "Defender";
$lang['info'][608]['description'] = "The defender is a member of the imperial army. His zeal in his work allows him to build a formidable defense in a short time in the colonies hostile.<br><br><font color=red>increasing 50% of defense production speed per level. Max levels: 2</font>";
$lang['info'][609]['name']        = "Bunker";
$lang['info'][609]['description'] = "The emperor has noticed the impressive work that you have provided to its empire. To thank you it gives you the chance to become Bunker. The Bunker is the highest award of the Mining sector of the imperial army.<br><br><font color=red>Clearing Planet protection</font>";
$lang['info'][610]['name']        = "Spy";
$lang['info'][610]['description'] = "The spy is a person enigmatic. Nobody has ever seen his face real, unless they are already dead.<br><br><font color=red>+5 Espionage levels per level. Max levels: 2</font>";
$lang['info'][611]['name']        = "Commander";
$lang['info'][611]['description'] = "The commander of the imperial army has mastered the art of handling fleets. His brain can calculate the trajectories of many fleet, much more than that of a normal human.<br><br><font color=red>+3 fleet slots per level. Max levels: 3</font> ";
$lang['info'][612]['name']        = "Destroyer";
$lang['info'][612]['description'] = "The destroyer is a ruthless officer. He massacred entire planet just for pleasure. It is currently developing a new method of producing the deathstars.<br><br><font color=red>2 RIP built instead of one.</font>";
$lang['info'][613]['name']        = "General";
$lang['info'][613]['description'] = "The venerable General is a person who has served for many years in the army. The workers, manufacturer of ships, produce faster in his presence.<br><br><font color=red>+25% of ships building speed per level. Max levels: 3</font>";
$lang['info'][614]['name']        = "Raider";
$lang['info'][614]['description'] = "The emperor has spotted you undeniable qualities of conqueror. It makes you stiffness. The stiffness is the highest rank in the industry stiffness of the imperial army<br><br><font color=red>Unlocking the SuperNova</font>";
$lang['info'][615]['name']        = "Emperer";
$lang['info'][615]['description'] = "You have shown that you are the greatest conqueror of the universe. Now it is time for you, to take the place that is yours.<br><br><font color=red>Clearing the Planet destroyer</font>";

?>