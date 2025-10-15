const { Document, Packer, Paragraph, HeadingLevel, TextRun, AlignmentType, PageBreak, TableOfContents, Header, Footer, PageNumber, convertInchesToTwip } = require("docx");
const fs = require("fs");

function title(text) {
	return new Paragraph({ text, heading: HeadingLevel.TITLE, alignment: AlignmentType.CENTER });
}
function h2(text) { return new Paragraph({ text, heading: HeadingLevel.HEADING_2 }); }
function p(text) { return new Paragraph({ children: [new TextRun({ text, size: 22 })], spacing: { after: 200 } }); }
function bullet(items) { return items.map((t) => new Paragraph({ text: t, bullet: { level: 0 } })); }

async function main() {
	const PREPARED_BY = "Amina Daghari";
	const ORG = "PhysicsWorks";
	const DATE_STR = new Date().toLocaleDateString("en-GB", { day: "2-digit", month: "long", year: "numeric" });
	const header = new Header({
		children: [
			new Paragraph({
				children: [
					new TextRun({ text: "SwiftNotify — Final Report", bold: true, size: 20 }),
					new TextRun({ text: "\t\t\t\t\t" }),
					new TextRun({ text: "Tataouine SMS Alert Platform", italics: true, size: 20 }),
				],
				spacing: { after: 120 },
			}),
		],
	});

	const footer = new Footer({
		children: [
			new Paragraph({
				children: [
					new TextRun({ text: "Page " }),
					PageNumber.CURRENT,
					new TextRun({ text: " of " }),
					PageNumber.TOTAL_PAGES,
				],
				alignment: AlignmentType.CENTER,
			}),
		],
	});

	const coverSection = {
		properties: { page: { margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 } } },
		headers: { default: header },
		footers: { default: footer },
		children: [
			new Paragraph({ children: [new TextRun({ text: "SwiftNotify: Tataouine SMS Alert Platform", size: 48, bold: true })], alignment: AlignmentType.CENTER, spacing: { after: 200 } }),
			new Paragraph({ children: [new TextRun({ text: "Final Internship Report", size: 36, bold: true })], alignment: AlignmentType.CENTER, spacing: { after: 400 } }),
			new Paragraph({ children: [new TextRun({ text: "Prepared by:", bold: true, size: 26 })], alignment: AlignmentType.CENTER }),
			new Paragraph({ children: [new TextRun({ text: PREPARED_BY, size: 26 })], alignment: AlignmentType.CENTER, spacing: { after: 200 } }),
			new Paragraph({ children: [new TextRun({ text: "Submitted to:", bold: true, size: 26 })], alignment: AlignmentType.CENTER }),
			new Paragraph({ children: [new TextRun({ text: ORG, size: 26 })], alignment: AlignmentType.CENTER, spacing: { after: 200 } }),
			new Paragraph({ children: [new TextRun({ text: "In partial fulfillment of the requirements for the Internship Program", size: 22 })], alignment: AlignmentType.CENTER, spacing: { after: 200 } }),
			new Paragraph({ children: [new TextRun({ text: DATE_STR, size: 24 })], alignment: AlignmentType.CENTER }),
			new Paragraph({ children: [new PageBreak()] }),
		],
	};

	const tocSection = {
		properties: { page: { margin: { top: 720, right: 720, bottom: 720, left: 720 } } },
		headers: { default: header },
		footers: { default: footer },
		children: [
			new Paragraph({ text: "Table of Contents", heading: HeadingLevel.HEADING_1 }),
			new TableOfContents("", {
				headingStyleRange: {
					from: HeadingLevel.HEADING_1,
					to: HeadingLevel.HEADING_3,
				},
				rightTabStopPosition: convertInchesToTwip(6.5),
			}),
			new Paragraph({ children: [new PageBreak()] }),
		],
	};

	const bodySection = {
		properties: { page: { margin: { top: 720, right: 720, bottom: 720, left: 720 } } },
		headers: { default: header },
		footers: { default: footer },
		children: [
			new Paragraph({ text: "1.0 Executive Summary", heading: HeadingLevel.HEADING_1 }),
			p("This report documents the design, development, and implementation of SwiftNotify, a comprehensive web-based SMS alert platform, as the capstone project of my internship at PhysicsWorks. SwiftNotify addresses a critical communication gap in the Tataouine region by enabling government agents to send timely, targeted emergency and informational alerts to residents who primarily rely on basic mobile phones without internet access."),
			p("The project successfully delivers a full-stack solution built with the Laravel and Vue.js frameworks, featuring intelligent group targeting, geographic zone management, real-time delivery tracking, and detailed cost analytics. The platform prioritizes security through Role-Based Access Control (RBAC) and OAuth, and offers a modern, responsive user interface."),

			new Paragraph({ text: "2.0 Project Background & Objectives", heading: HeadingLevel.HEADING_1 }),
			p("The Tataouine region required a robust system to disseminate critical information (e.g., weather warnings, public safety alerts, utility outages) to its population. Existing methods were often slow, non-targeted, and costly. SwiftNotify was conceived to solve this with the following primary objectives:"),
			new Paragraph({ text: "Reliable Communication: Provide a fail-safe method to send SMS alerts instantly, ensuring critical information reaches all citizens, regardless of smartphone ownership.", bullet: { level: 0 } }),
			new Paragraph({ text: "Precision Targeting: Allow authorized agents to send messages to specific demographic groups or geographic areas defined on an interactive map.", bullet: { level: 0 } }),
			new Paragraph({ text: "Cost Optimization: Reduce unnecessary SMS costs by providing agents with smart suggestions and transparent analytics on spending.", bullet: { level: 0 } }),
			new Paragraph({ text: "Security & Access Control: Implement secure authentication with roles (Admin/Agent) and Google OAuth for desktop access.", bullet: { level: 0 } }),
			new Paragraph({ text: "User-Centric Design: Deliver an intuitive, cohesive, and accessible dark-mode interface.", bullet: { level: 0 } }),

			new Paragraph({ text: "3.0 System Architecture & Technology Stack", heading: HeadingLevel.HEADING_1 }),
			p("SwiftNotify employs a modern, decoupled architecture for scalability and maintainability."),
			new Paragraph({ text: "Backend (API & Business Logic): Laravel 10.x (PHP 8.2+), MySQL 8.x database.", bullet: { level: 0 } }),
			new Paragraph({ text: "Frontend (User Interface): Vue 3 Composition API, Inertia.js, Tailwind CSS.", bullet: { level: 0 } }),
			new Paragraph({ text: "External Services & Integrations:", bullet: { level: 0 } }),
			new Paragraph({ text: "Twilio Programmable SMS API with webhooks for delivery status callbacks.", bullet: { level: 1 } }),
			new Paragraph({ text: "Leaflet.js + Leaflet.draw for rendering and creating geographic zones.", bullet: { level: 1 } }),
			new Paragraph({ text: "Laravel Fortify + Socialite (Google OAuth).", bullet: { level: 1 } }),
			p("Flow: Compose → validate → resolve recipients (groups/area/all) → Twilio send → recipients saved with cost → webhook updates → history and analytics."),

			new Paragraph({ text: "4.0 Key Features & Implementation", heading: HeadingLevel.HEADING_1 }),
			new Paragraph({ text: "4.1 Intelligent Group & Contact Management", heading: HeadingLevel.HEADING_2 }),
			p("A ContactGroup model with fields for category, tags, and a geometry column (GeoJSON polygons). A SmartGroupService calculates relevance scores for groups based on message content and context. CSV import supports bulk contacts; Leaflet.draw enables visual definition of areas."),
			new Paragraph({ text: "4.2 Message Composition & Intelligence", heading: HeadingLevel.HEADING_2 }),
			p("A MessageTemplate system with pre-defined categories and keyword detection. The backend analyzes draft messages to suggest the most appropriate templates and recipient groups."),
			new Paragraph({ text: "4.3 SMS Campaign Management", heading: HeadingLevel.HEADING_2 }),
			p("The MessageController handles creation and sending via Twilio; MessageRecipient tracks each SMS status (queued, sending, sent, failed). SmsPricing provides accurate cost lookup and totals."),
			new Paragraph({ text: "4.4 Dashboards & Analytics", heading: HeadingLevel.HEADING_2 }),
			p("Admin and Agent dashboards provide KPIs, user management, personal history, success rates, and cost summaries."),
			new Paragraph({ text: "4.5 Security & Authentication", heading: HeadingLevel.HEADING_2 }),
			p("Laravel Fortify powers RBAC; email verification is required for agents; Google OAuth is available for desktop users."),

			new Paragraph({ text: "5.0 Technical Challenges & Solutions", heading: HeadingLevel.HEADING_1 }),
			new Paragraph({ text: "Challenge: Database schema mismatch (sms_pricings vs sms_pricing). Solution: explicit model table name 'sms_pricing' in SmsPricing.", bullet: { level: 0 } }),
			new Paragraph({ text: "Challenge: Duplicate columns during migrations. Solution: guard with Schema::hasColumn checks.", bullet: { level: 0 } }),
			new Paragraph({ text: "Challenge: Inertia response coherence. Solution: standardized controller responses, used router.post for robust submissions.", bullet: { level: 0 } }),
			new Paragraph({ text: "Challenge: Legacy/invalid GeoJSON. Solution: robust parsing with fallbacks; ensured geometry is fillable; safe previews.", bullet: { level: 0 } }),

			new Paragraph({ text: "6.0 Personal Contribution & Learning Outcomes", heading: HeadingLevel.HEADING_1 }),
			p("As the sole developer, I handled requirements analysis, system design, full-stack development, third-party integrations (Twilio, Google OAuth), problem solving, and QA."),
			new Paragraph({ text: "Full-Stack Proficiency (Laravel/Vue, spatial data, queues, OAuth).", bullet: { level: 0 } }),
			new Paragraph({ text: "API Design & Integration (Twilio webhooks, OAuth).", bullet: { level: 0 } }),
			new Paragraph({ text: "Production-Ready Mindset (defensive code, resilient migrations).", bullet: { level: 0 } }),
			new Paragraph({ text: "Project Management (iterative delivery, self-directed work).", bullet: { level: 0 } }),

			new Paragraph({ text: "7.0 Conclusion & Future Recommendations", heading: HeadingLevel.HEADING_1 }),
			p("SwiftNotify has been successfully developed into an MVP that fulfills all initial objectives. It provides a powerful, secure, and user-friendly platform for targeted SMS communication in the Tataouine region. Future priorities: webhook reliability (retries), advanced analytics, multilingual templates, observability, backups, and fine-grained roles."),

			new Paragraph({ text: "References", heading: HeadingLevel.HEADING_1 }),
			p("[Add references: Laravel, Vue, Inertia, Tailwind, Twilio, Leaflet]"),

			new Paragraph({ text: "Appendices", heading: HeadingLevel.HEADING_1 }),
			new Paragraph({ text: "Appendix A: GitHub Repository Link", heading: HeadingLevel.HEADING_2 }),
			p("[Insert link]"),
			new Paragraph({ text: "Appendix B: Application Screenshots", heading: HeadingLevel.HEADING_2 }),
			p("Screenshot 1: Admin Dashboard — [Insert image]"),
			p("Screenshot 2: Geographic Targeting — [Insert image]"),
			p("Screenshot 3: Message Composer with Smart Suggestions — [Insert image]"),
		],
	};

	const doc = new Document({
		features: { updateFields: true },
		sections: [coverSection, tocSection, bodySection],
	});

	const buffer = await Packer.toBuffer(doc);
	const outPath = "SwiftNotify_Final_Report_Cover.docx";
	fs.writeFileSync(outPath, buffer);
	console.log(`Generated: ${outPath}`);
}

main().catch((e) => { console.error(e); process.exit(1); });


