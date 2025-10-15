const { Document, Packer, Paragraph, HeadingLevel, TextRun, AlignmentType, Table, TableRow, TableCell, WidthType, BorderStyle, PageOrientation, Footer, Header, ImageRun } = require("docx");
const fs = require("fs");

function title(text) {
	return new Paragraph({
		text,
		heading: HeadingLevel.TITLE,
		alignment: AlignmentType.CENTER,
	});
}

function h2(text) {
	return new Paragraph({ text, heading: HeadingLevel.HEADING_2 });
}

function h3(text) {
	return new Paragraph({ text, heading: HeadingLevel.HEADING_3 });
}

function p(text) {
	return new Paragraph({
		children: [new TextRun({ text, size: 22 })],
		spacing: { after: 200 },
	});
}

function bullet(items) {
	return items.map((t) => new Paragraph({ text: t, bullet: { level: 0 } }));
}

function tableTwoCols(left, right) {
	return new Table({
		width: { size: 100, type: WidthType.PERCENTAGE },
		rows: [
			new TableRow({
				children: [
					new TableCell({ children: [p(left)], width: { size: 50, type: WidthType.PERCENTAGE } }),
					new TableCell({ children: [p(right)], width: { size: 50, type: WidthType.PERCENTAGE } }),
				],
			}),
		],
	});
}

async function main() {
	const doc = new Document({
		sections: [
			{
				properties: {
					page: { margin: { top: 720, right: 720, bottom: 720, left: 720 } },
				},
				children: [
					title("SwiftNotify — Tataouine SMS Alert Platform (Final Report)"),
					p("Prepared by: [Your Name] | Organization: PhysicsWorks | Date: [Date]"),

					h2("Executive Summary"),
					p("SwiftNotify is a Laravel + Vue (Inertia) platform enabling authorized agents in Tataouine to send targeted SMS alerts to residents using basic phones. It delivers smart group suggestions, map-based targeting, delivery tracking, cost analytics, and a cohesive dark-mode UI. The solution addresses urgent local communication needs while remaining simple to operate and scalable."),

					h2("Objectives"),
					...bullet([
						"Enable fast, reliable SMS alerts to residents without internet access.",
						"Provide targeted delivery by group and geographic area.",
						"Reduce noise and costs via smart suggestions and cost analytics.",
						"Ensure secure access with RBAC, email verification, and Google OAuth (desktop).",
						"Deliver a modern, consistent dark-mode UI across all pages.",
					]),

					h2("Scope and Stakeholders"),
					p("Scope: Messaging, Contact/Group management, Smart suggestions/templates, SMS cost tracking, Dashboards, System settings, Email verification, Google OAuth (desktop)."),
					p("Stakeholders: Municipal/agency admins, field agents, residents (recipients)."),

					h2("System Architecture"),
					...bullet([
						"Backend: Laravel 10+ (PHP 8.2+), MySQL.",
						"Frontend: Vue 3 + Inertia.js, Tailwind CSS (dark mode).",
						"SMS: Twilio API + status webhooks.",
						"Maps: Leaflet + Leaflet.draw for area targeting.",
						"Auth: Laravel Fortify; roles Admin/Agent; Google OAuth (desktop).",
						"Build: Vite (HMR configured for 127.0.0.1 and LAN scenarios).",
					]),
					p("Flow: Compose → validate → resolve recipients (groups/area/all) → Twilio send → recipients saved with cost → webhook updates → history and analytics."),

					h2("Key Features"),
					...bullet([
						"Smart Group Management: categories/tags, relevance scoring, suggested combinations.",
						"Message Intelligence: templates by category, keyword detection, suggested content.",
						"SMS Sending: group/zone/all, progress bar, delivery stats, history.",
						"Cost Tracking: per-recipient cost with totals and analytics.",
						"Contacts & Groups: CSV import; draw and store group areas as GeoJSON.",
						"Dashboards: Admin and Agent views with KPIs and recent activity.",
						"Settings: SMS provider config and general system options.",
						"Security: RBAC, Google OAuth (desktop), email verification.",
					]),

					h2("Implementation Highlights"),
					...bullet([
						"ContactGroup: category, tags, description, is_active, geometry with relevance helpers.",
						"MessageTemplate: categories/keywords with defaults for Tataouine.",
						"SmartGroupService: suggests groups/combinations/templates from message content.",
						"MessageRecipient: normalized Twilio statuses (queued/sending → sent).",
						"SmsPricing: explicit table name 'sms_pricing'.",
						"MessageController: safe defaults, progress stats response, smart suggestions endpoint.",
						"Frontend: Messages, History, Contacts, Groups, Dashboards, Settings with dark-mode.",
						"Build: vite.config.js HMR/CORS; Google OAuth dynamic redirect.",
					]),

					h2("UI/UX"),
					p("Consistent dark-mode, accessible contrasts, progress feedback on send, modal summaries, resilient empty/error states, and map previews with clear fallbacks for invalid GeoJSON."),

					h2("Security and Access Control"),
					...bullet([
						"RBAC: Admin and Agent roles enforced server-side.",
						"Authentication: Fortify (email/password), Google OAuth for desktop.",
						"Email verification: SMTP (Gmail App Password).",
						"Note: OAuth on LAN IP requires a public HTTPS tunnel.",
					]),

					h2("Testing and Quality Assurance"),
					...bullet([
						"End-to-end send tests; delivery updates; cost sums; history pagination.",
						"Validation and safe defaults on backend and frontend.",
						"GeoJSON robustness with try/catch and visual fallbacks.",
						"UI QA: dark-mode consistency, text contrast, pagination/link safety.",
						"Structured logging (backend) and console tracing (frontend).",
					]),

					h2("Challenges and Resolutions"),
					...bullet([
						"Table name mismatch (sms_pricings vs sms_pricing): fixed with explicit model table.",
						"Duplicate migration columns: guarded with Schema::hasColumn.",
						"Inertia response expectations: used router.post and coherent responses.",
						"Status accuracy: mapped Twilio queued/sending to sent.",
						"Legacy GeoJSON: fallbacks; geometry fillable; safer previews.",
						"CORS/HMR on LAN: standardized host/HMR config.",
						"OAuth limits on LAN: documented HTTPS tunnel approach.",
					]),

					h2("Outcomes and Impact"),
					p("Reliable SMS delivery with clear progress and delivery stats; targeted alerts reduce noise; cost analytics inform usage; modern dark UI improves operator efficiency; clear guidance for development and demos."),

					h2("Future Work"),
					...bullet([
						"Webhook reliability (retries, signed callbacks).",
						"Advanced analytics: per-zone/per-category trends.",
						"Multilingual templates (AR/FR) and personalization.",
						"Rate limiting, observability, backups, DR.",
						"Audit logs and fine-grained permissions.",
					]),

					h2("Conclusion"),
					p("SwiftNotify provides a solid, production-minded foundation for urgent community alerts in Tataouine, balancing reliable delivery, smart targeting, and cost visibility with an approachable UI."),
				],
			},
		],
	});

	const buffer = await Packer.toBuffer(doc);
	const outPath = "SwiftNotify_Final_Report.docx";
	fs.writeFileSync(outPath, buffer);
	console.log(`Generated: ${outPath}`);
}

main().catch((e) => {
	console.error(e);
	process.exit(1);
});



